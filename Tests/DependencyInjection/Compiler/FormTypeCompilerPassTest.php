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

use Ivory\CKEditorBundle\DependencyInjection\Compiler\FormTypeCompilerPass;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Form type compiler pass test.
 */
class FormTypeCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Symfony\Component\DependencyInjection\ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject */
    private $containerBuilderMock;

    /** @var \Symfony\Component\DependencyInjection\Definition|\PHPUnit_Framework_MockObject_MockObject */
    private $mockDefinition;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->containerBuilderMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->setMethods(array('getDefinition', 'hasDefinition'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockDefinition = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->setMethods(array('addTag', 'clearTag'))
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->containerBuilderMock);
        unset($this->mockDefinition);
    }

    public function testFormTypeAliasAddedForSymfony2()
    {
        // This test applies only to Symfony 2.x
        if (Kernel::VERSION_ID >= 30000) {
            $this->markTestSkipped('This test applies only to Symfony 2.x');
        }

        $this->mockDefinition
            ->expects($this->once())
            ->method('addTag')
            ->with('form.type', array('alias' => 'ckeditor'))
            ->will($this->returnSelf());

        $this->mockDefinition
            ->expects($this->once())
            ->method('clearTag')
            ->with('form.type')
            ->will($this->returnSelf());

        $this->containerBuilderMock
            ->expects($this->once())
            ->method('getDefinition')
            ->with('ivory_ck_editor.form.type')
            ->will($this->returnValue($this->mockDefinition));

        $this->containerBuilderMock
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('ivory_ck_editor.form.type')
            ->will($this->returnValue(true));

        $compilerPass = new FormTypeCompilerPass();

        $compilerPass->process($this->containerBuilderMock);
    }

    public function testFormTypeAliasNotAddedForSymfony3()
    {
        // This test applies only to Symfony 3.x
        if (Kernel::VERSION_ID < 30000) {
            $this->markTestSkipped('This test applies only to Symfony 3.x');
        }

        $this->containerBuilderMock
            ->expects($this->never())
            ->method('hasDefinition')
            ->with('ivory_ck_editor.form.type');

        $compilerPass = new FormTypeCompilerPass();

        $compilerPass->process($this->containerBuilderMock);
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @expectedExceptionMessage You have requested a non-existent service "ivory_ck_editor.form.type".
     */
    public function testMissingServiceForSymfony2()
    {
        // This test applies only to Symfony 2.x
        if (Kernel::VERSION_ID >= 30000) {
            $this->markTestSkipped('This test applies only to Symfony 2.x');
        }

        $this->containerBuilderMock
            ->expects($this->once())
            ->method('hasDefinition')
            ->with('ivory_ck_editor.form.type')
            ->will($this->returnValue(false));

        $compilerPass = new FormTypeCompilerPass();

        $compilerPass->process($this->containerBuilderMock);
    }
}
