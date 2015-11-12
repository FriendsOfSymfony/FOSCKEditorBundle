<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests;

use Ivory\CKEditorBundle\IvoryCKEditorBundle;

/**
 * Ivory CKEditor bundle test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
class IvoryCKEditorBundleTest extends \PHPUnit_Framework_TestCase
{
    public function testBundle()
    {
        $bundle = new IvoryCKEditorBundle();

        $this->assertInstanceOf('Symfony\Component\HttpKernel\Bundle\Bundle', $bundle);
    }

    public function testCompilerPasses()
    {
        $containerBuilder = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('addCompilerPass'))
            ->getMock();

        $containerBuilder
            ->expects($this->at(0))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf('Ivory\CKEditorBundle\DependencyInjection\Compiler\ResourceCompilerPass'))
            ->will($this->returnSelf());

        $containerBuilder
            ->expects($this->at(1))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf('Ivory\CKEditorBundle\DependencyInjection\Compiler\AssetsHelperCompilerPass'))
            ->will($this->returnSelf());

        $containerBuilder
            ->expects($this->at(2))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf('Ivory\CKEditorBundle\DependencyInjection\Compiler\FormTypeCompilerPass'))
            ->will($this->returnSelf());

        $bundle = new IvoryCKEditorBundle();
        $bundle->build($containerBuilder);
    }
}
