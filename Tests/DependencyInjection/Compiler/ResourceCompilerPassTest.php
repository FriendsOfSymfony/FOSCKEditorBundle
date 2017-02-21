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
use Ivory\CKEditorBundle\Tests\AbstractTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ResourceCompilerPassTest extends AbstractTestCase
{
    /**
     * @var ResourceCompilerPass
     */
    private $compilerPass;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->compilerPass = new ResourceCompilerPass();
    }

    public function testTwigResource()
    {
        $containerBuilder = $this->createContainerBuilderMock();
        $containerBuilder
            ->expects($this->exactly(2))
            ->method('hasParameter')
            ->will($this->returnValueMap([
                ['templating.helper.form.resources', false],
                [$parameter = 'twig.form.resources', true],
            ]));

        $containerBuilder
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo($parameter))
            ->will($this->returnValue([$template = 'layout.html.twig']));

        $containerBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->identicalTo($parameter),
                $this->identicalTo([
                    'IvoryCKEditorBundle:Form:ckeditor_widget.html.twig',
                    $template,
                ])
            );

        $this->compilerPass->process($containerBuilder);
    }

    public function testPhpResource()
    {
        $containerBuilder = $this->createContainerBuilderMock();
        $containerBuilder
            ->expects($this->exactly(2))
            ->method('hasParameter')
            ->will($this->returnValueMap([
                [$parameter = 'templating.helper.form.resources', true],
                ['twig.form.resources', false],
            ]));

        $containerBuilder
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->identicalTo($parameter))
            ->will($this->returnValue([$template = 'layout.html.php']));

        $containerBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with(
                $this->identicalTo($parameter),
                $this->identicalTo(['IvoryCKEditorBundle:Form', $template])
            );

        $this->compilerPass->process($containerBuilder);
    }

    /**
     * @return ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createContainerBuilderMock()
    {
        return $this->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['hasParameter', 'getParameter', 'setParameter'])
            ->getMock();
    }
}
