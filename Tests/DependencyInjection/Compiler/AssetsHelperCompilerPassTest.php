<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests\DependencyInjection\Compiler;

use Ivory\CKEditorBundle\DependencyInjection\Compiler\AssetsHelperCompilerPass;
use Ivory\CKEditorBundle\Tests\AbstractTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
class AssetsHelperCompilerPassTest extends AbstractTestCase
{
    /**
     * @var AssetsHelperCompilerPass
     */
    private $compilerPass;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->compilerPass = new AssetsHelperCompilerPass();
    }

    public function testAssetsPackagesAliasWithTemplatingHelperAssets()
    {
        if (Kernel::VERSION_ID >= 20700) {
            $this->markTestSkipped();
        }

        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with($this->identicalTo($legacy = 'templating.helper.assets'))
            ->will($this->returnValue(true));

        $container
            ->expects($this->once())
            ->method('setAlias')
            ->with(
                $this->identicalTo('assets.packages'),
                $this->identicalTo($legacy)
            );

        $this->compilerPass->process($container);
    }

    public function testAssetsPackagesAliasWithoutTemplatingHelperAssets()
    {
        if (Kernel::VERSION_ID >= 20700) {
            $this->markTestSkipped();
        }

        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->once())
            ->method('hasDefinition')
            ->with($this->identicalTo($legacy = 'templating.helper.assets'))
            ->will($this->returnValue(false));

        $container
            ->expects($this->never())
            ->method('setAlias');

        $this->compilerPass->process($container);
    }

    public function testAssetsPackagesAliasWithAssetsPackage()
    {
        if (Kernel::VERSION_ID < 20700) {
            $this->markTestSkipped();
        }

        $container = $this->createContainerBuilderMock();
        $container
            ->expects($this->never())
            ->method('setAlias');

        $this->compilerPass->process($container);
    }

    /**
     * @return ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createContainerBuilderMock()
    {
        return $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('hasDefinition', 'setAlias'))
            ->getMock();
    }
}
