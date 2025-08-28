<?php

/*
 * This file is part of the FOSCKEditor Bundle.
 *
 * (c) 2018 - present  Friends of Symfony
 * (c) 2009 - 2017     Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\CKEditorBundle\Tests\Form\Type;

use FOS\CKEditorBundle\Config\CKEditorConfiguration;
use FOS\CKEditorBundle\DependencyInjection\Configuration;
use FOS\CKEditorBundle\Exception\ConfigException;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorTypeTest extends TestCase
{
    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var CKEditorType
     */
    private $ckEditorType;

    /**
     * @var string
     */
    private $formType;

    protected function setUp(): void
    {
        $this->ckEditorType = new CKEditorType(new CKEditorConfiguration(
            (new Processor())->processConfiguration(new Configuration(), [])
        ));

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->ckEditorType)
            ->getFormFactory();

        $this->formType = CKEditorType::class;
    }

    public function testEnableWithDefaultValue(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('enable', $view->vars);
        $this->assertTrue($view->vars['enable']);
    }

    public function testEnableWithExplicitValue(): void
    {
        $form = $this->factory->create($this->formType, null, ['enable' => false]);
        $view = $form->createView();

        $this->assertArrayHasKey('enable', $view->vars);
        $this->assertFalse($view->vars['enable']);
    }

    public function testBaseAndJsPathWithDefaultValues(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('base_path', $view->vars);
        $this->assertSame('bundles/fosckeditor/', $view->vars['base_path']);

        $this->assertArrayHasKey('js_path', $view->vars);
        $this->assertSame('bundles/fosckeditor/ckeditor.js', $view->vars['js_path']);
    }

    public function testBaseAndJsPathWithExplicitValues(): void
    {
        $form = $this->factory->create(
            $this->formType,
            null,
            [
                'base_path' => 'foo',
                'js_path' => 'foo/ckeditor.js',
            ]
        );

        $view = $form->createView();

        $this->assertArrayHasKey('base_path', $view->vars);
        $this->assertSame('foo/', $view->vars['base_path']);

        $this->assertArrayHasKey('js_path', $view->vars);
        $this->assertSame('foo/ckeditor.js', $view->vars['js_path']);
    }

    public function testDefaultConfig(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertEmpty($view->vars['config']);
    }

    public function testConfigWithExplicitConfig(): void
    {
        $options = [
            'config' => [
                'toolbar' => ['foo' => 'bar'],
                'uiColor' => '#ffffff',
            ],
        ];

        $form = $this->factory->create($this->formType, null, $options);
        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertSame($options['config'], $view->vars['config']);
    }

    public function testDefaultPlugins(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('plugins', $view->vars);
        $this->assertEmpty($view->vars['plugins']);
    }

    public function testPluginsWithExplicitPlugins(): void
    {
        $plugins = [
            'wordcount' => [
                'path' => '/my/path',
                'filename' => 'plugin.js',
            ],
        ];

        $form = $this->factory->create($this->formType, null, ['plugins' => $plugins]);

        $view = $form->createView();

        $this->assertArrayHasKey('plugins', $view->vars);
        $this->assertSame($plugins, $view->vars['plugins']);
    }

    public function testExplicitDisable(): void
    {
        $options = [
            'enable' => false,
            'config' => [
                'toolbar' => ['foo' => 'bar'],
                'uiColor' => '#ffffff',
            ],
            'plugins' => [
                'wordcount' => [
                    'path' => '/my/path',
                    'filename' => 'plugin.js',
                ],
            ],
        ];

        $form = $this->factory->create($this->formType, null, $options);
        $view = $form->createView();

        $this->assertArrayHasKey('enable', $view->vars);
        $this->assertFalse($view->vars['enable']);

        $this->assertArrayNotHasKey('autoload', $view->vars);
        $this->assertArrayNotHasKey('config', $view->vars);
        $this->assertArrayNotHasKey('plugins', $view->vars);
        $this->assertArrayNotHasKey('stylesheets', $view->vars);
        $this->assertArrayNotHasKey('templates', $view->vars);
    }

    public function testBadConfig(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('The CKEditor config "nop" does not exist.');

        $form = $this->factory->create($this->formType, null, ['config_name' => 'nop']);
        $form->createView();
    }
}
