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

use Ivory\CKEditorBundle\DependencyInjection\Compiler\ResourceCompilerPass;

/**
 * Resource compiler pass test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Ivory\CKEditorBundle\DependencyInjection\Compiler\ResourceCompilerPass */
    private $compilerPass;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->compilerPass = new ResourceCompilerPass();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->compilerPass);
    }

    public function testTwigResource()
    {
        $containerBuilder = $this->createContainerBuilderMock();
        $containerBuilder
            ->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValueMap(array(
                array('templating.engines', array('twig')),
                array($parameter = 'twig.form.resources', array($template = 'foo')),
            )));

        $containerBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->identicalTo($parameter),
                $this->identicalTo(array('IvoryCKEditorBundle:Form:ckeditor_widget.html.twig', $template))
            );

        $this->compilerPass->process($containerBuilder);
    }

    public function testPhpResource()
    {
        $containerBuilder = $this->createContainerBuilderMock();
        $containerBuilder
            ->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValueMap(array(
                array('templating.engines', array('php')),
                array($parameter = 'templating.helper.form.resources', array($template = 'foo')),
            )));

        $containerBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->identicalTo($parameter),
                $this->identicalTo(array('IvoryCKEditorBundle:Form', $template))
            );

        $this->compilerPass->process($containerBuilder);
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
            ->setMethods(array('getParameter', 'setParameter'))
            ->getMock();
    }
}
