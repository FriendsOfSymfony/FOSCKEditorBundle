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

    public function testAsyncWithDefaultValue(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('async', $view->vars);
        $this->assertFalse($view->vars['async']);
    }

    public function testAsyncWithExplicitValue(): void
    {
        $form = $this->factory->create($this->formType, null, ['async' => true]);
        $view = $form->createView();

        $this->assertArrayHasKey('async', $view->vars);
        $this->assertTrue($view->vars['async']);
    }

    public function testAutoloadWithDefaultValue(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('autoload', $view->vars);
        $this->assertTrue($view->vars['autoload']);
    }

    public function testAutoloadWithExplicitValue(): void
    {
        $form = $this->factory->create($this->formType, null, ['autoload' => false]);
        $view = $form->createView();

        $this->assertArrayHasKey('autoload', $view->vars);
        $this->assertFalse($view->vars['autoload']);
    }

    public function testAutoInlineWithDefaultValue(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('auto_inline', $view->vars);
        $this->assertTrue($view->vars['auto_inline']);
    }

    public function testAutoInlineWithExplicitValue(): void
    {
        $form = $this->factory->create($this->formType, null, ['auto_inline' => false]);
        $view = $form->createView();

        $this->assertArrayHasKey('auto_inline', $view->vars);
        $this->assertFalse($view->vars['auto_inline']);
    }

    public function testInlineWithDefaultValue(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('inline', $view->vars);
        $this->assertFalse($view->vars['inline']);
    }

    public function testInlineWithExplicitValue(): void
    {
        $form = $this->factory->create($this->formType, null, ['inline' => true]);
        $view = $form->createView();

        $this->assertArrayHasKey('inline', $view->vars);
        $this->assertTrue($view->vars['inline']);
    }

    public function testJqueryWithDefaultValue(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('jquery', $view->vars);
        $this->assertFalse($view->vars['jquery']);
    }

    public function testJqueryWithExplicitValue(): void
    {
        $form = $this->factory->create($this->formType, null, ['jquery' => true]);
        $view = $form->createView();

        $this->assertArrayHasKey('jquery', $view->vars);
        $this->assertTrue($view->vars['jquery']);
    }

    public function testInputSyncWithDefaultValue(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('input_sync', $view->vars);
        $this->assertFalse($view->vars['input_sync']);
    }

    public function testInputSyncWithExplicitValue(): void
    {
        $form = $this->factory->create($this->formType, null, ['input_sync' => true]);
        $view = $form->createView();

        $this->assertArrayHasKey('input_sync', $view->vars);
        $this->assertTrue($view->vars['input_sync']);
    }

    public function testRequireJsWithDefaultValue(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('require_js', $view->vars);
        $this->assertFalse($view->vars['require_js']);
    }

    public function testRequireJsWithExplicitValue(): void
    {
        $form = $this->factory->create($this->formType, null, ['require_js' => true]);
        $view = $form->createView();

        $this->assertArrayHasKey('require_js', $view->vars);
        $this->assertTrue($view->vars['require_js']);
    }

    public function testFilebrowsersWithDefaultValue(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('filebrowsers', $view->vars);
        $this->assertEmpty($view->vars['filebrowsers']);
    }

    public function testFilebrowsersWithExplicitValue(): void
    {
        $form = $this->factory->create($this->formType, null, ['filebrowsers' => $filebrowsers = [
            'VideoBrowse',
            'VideoUpload',
        ]]);

        $view = $form->createView();

        $this->assertArrayHasKey('filebrowsers', $view->vars);
        $this->assertSame($filebrowsers, $view->vars['filebrowsers']);
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

    public function testJqueryPathWithDefaultValue(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('jquery_path', $view->vars);
        $this->assertSame('bundles/fosckeditor/adapters/jquery.js', $view->vars['jquery_path']);
    }

    public function testJqueryPathWithExplicitValue(): void
    {
        $form = $this->factory->create($this->formType, null, ['jquery_path' => 'foo/jquery.js']);
        $view = $form->createView();

        $this->assertArrayHasKey('jquery_path', $view->vars);
        $this->assertSame('foo/jquery.js', $view->vars['jquery_path']);
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

    public function testDefaultStylesSet(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertEmpty($view->vars['styles']);
    }

    public function testPluginsWithExplicitStylesSet(): void
    {
        $stylesSets = [
            'default' => [
                ['name' => 'Blue Title', 'element' => 'h2', 'styles' => ['color' => 'Blue']],
                ['name' => 'CSS Style', 'element' => 'span', 'attributes' => ['class' => 'my_style']],
            ],
        ];

        $form = $this->factory->create($this->formType, null, ['styles' => $stylesSets]);

        $view = $form->createView();

        $this->assertSame($stylesSets, $view->vars['styles']);
    }

    public function testDefaultTemplates(): void
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertEmpty($view->vars['templates']);
    }

    public function testTemplatesWithExplicitTemplates(): void
    {
        $templates = [
            'default' => [
                'imagesPath' => '/my/path',
                'templates' => [
                    [
                        'title' => 'My Template',
                        'html' => '<h1>Template</h1><p>Type your text here.</p>',
                    ],
                ],
            ],
        ];

        $form = $this->factory->create($this->formType, null, ['templates' => $templates]);

        $view = $form->createView();

        $this->assertSame($templates, $view->vars['templates']);
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
