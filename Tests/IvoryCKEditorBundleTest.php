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
    /** @var \Ivory\CKEditorBundle\IvoryCKEditorBundle */
    private $bundle;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->bundle = new IvoryCKEditorBundle();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->bundle);
    }

    public function testBundle()
    {
        $this->assertInstanceOf('Symfony\Component\HttpKernel\Bundle\Bundle', $this->bundle);
    }

    public function testCompilerPasses()
    {
        $containerBuilder = $this->createContainerBuilderMock();
        $containerBuilder
            ->expects($this->at(0))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf('Ivory\CKEditorBundle\DependencyInjection\Compiler\AssetsHelperCompilerPass'))
            ->will($this->returnSelf());

        $containerBuilder
            ->expects($this->at(1))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf('Ivory\CKEditorBundle\DependencyInjection\Compiler\ResourceCompilerPass'))
            ->will($this->returnSelf());

        $containerBuilder
            ->expects($this->at(2))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf('Ivory\CKEditorBundle\DependencyInjection\Compiler\TemplatingCompilerPass'))
            ->will($this->returnSelf());

        $this->bundle->build($containerBuilder);
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createContainerBuilderMock()
    {
        return $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('addCompilerPass'))
            ->getMock();
    }
}
