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
use Symfony\Component\HttpKernel\Kernel;

/**
 * Assets helper compiler pass test.
 *
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 * @author GeLo <geloen.eric@gmail.com>
 */
class AssetsHelperCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\CKEditorBundle\DependencyInjection\Compiler\AssetsHelperCompilerPass */
    private $assetsHelperCompilerPass;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->assetsHelperCompilerPass = new AssetsHelperCompilerPass();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->containerBuilderMock);
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

        $this->assetsHelperCompilerPass->process($container);
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

        $this->assetsHelperCompilerPass->process($container);
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

        $this->assetsHelperCompilerPass->process($container);
    }

    /**
     * Creates a container builder mock.
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createContainerBuilderMock()
    {
        return $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->disableOriginalConstructor()
            ->setMethods(array('hasDefinition', 'setAlias'))
            ->getMock();
    }
}
