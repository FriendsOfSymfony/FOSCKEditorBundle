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

use Ivory\CKEditorBundle\DependencyInjection\Compiler\ResourceCompilerPass;
use Ivory\CKEditorBundle\DependencyInjection\Compiler\TemplatingCompilerPass;
use Ivory\CKEditorBundle\IvoryCKEditorBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
class IvoryCKEditorBundleTest extends AbstractTestCase
{
    /**
     * @var IvoryCKEditorBundle
     */
    private $bundle;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->bundle = new IvoryCKEditorBundle();
    }

    public function testBundle()
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }

    public function testCompilerPasses()
    {
        $containerBuilder = $this->createContainerBuilderMock();
        $containerBuilder
            ->expects($this->at(0))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(ResourceCompilerPass::class))
            ->will($this->returnSelf());

        $containerBuilder
            ->expects($this->at(1))
            ->method('addCompilerPass')
            ->with($this->isInstanceOf(TemplatingCompilerPass::class))
            ->will($this->returnSelf());

        $this->bundle->build($containerBuilder);
    }

    /**
     * @return ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createContainerBuilderMock()
    {
        return $this->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['addCompilerPass'])
            ->getMock();
    }
}
