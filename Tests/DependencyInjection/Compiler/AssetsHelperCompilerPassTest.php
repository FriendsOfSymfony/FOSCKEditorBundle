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
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Assets helper compiler pass test.
 *
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
class AssetsHelperCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Symfony\Component\Asset\Packages|\Symfony\Component\Templating\Helper\CoreAssetsHelper|\PHPUnit_Framework_MockObject_MockObject */
    private $containerBuilderMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->containerBuilderMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->setMethods(array('hasDefinition', 'setAlias'))
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->containerBuilderMock);
    }

    public function testAssetsPackagesExists()
    {
        $this->containerBuilderMock
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('assets.packages')
            ->will($this->returnValue(true));

        $compilerPass = new AssetsHelperCompilerPass();

        $compilerPass->process($this->containerBuilderMock);
    }

    public function testAssetsPackagesNotExists()
    {
        $this->containerBuilderMock
            ->expects($this->at(0))
            ->method('hasDefinition')
            ->with('assets.packages')
            ->will($this->returnValue(false));

        $this->containerBuilderMock
            ->expects($this->at(1))
            ->method('hasDefinition')
            ->with('templating.helper.assets')
            ->will($this->returnValue(true));

        $this->containerBuilderMock
            ->expects($this->once())
            ->method('setAlias')
            ->with($this->equalTo('assets.packages'), $this->equalTo('templating.helper.assets'));

        $compilerPass = new AssetsHelperCompilerPass();

        $compilerPass->process($this->containerBuilderMock);
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @expectedExceptionMessage You have requested a non-existent service "templating.helper.assets".
     */
    public function testMissingService()
    {
        $this->containerBuilderMock
            ->expects($this->at(0))
            ->method('hasDefinition')
            ->with('assets.packages')
            ->will($this->returnValue(false));

        $this->containerBuilderMock
            ->expects($this->at(1))
            ->method('hasDefinition')
            ->with('templating.helper.assets')
            ->will($this->returnValue(false));

        $compilerPass = new AssetsHelperCompilerPass();

        $compilerPass->process($this->containerBuilderMock);
    }
}
