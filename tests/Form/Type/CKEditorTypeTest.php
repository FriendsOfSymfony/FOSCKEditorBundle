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
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use FOS\CKEditorBundle\Tests\AbstractTestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorTypeTest extends AbstractTestCase
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

    /**
     * {@inheritdooc}.
     */
    protected function setUp()
    {
        $this->ckEditorType = new CKEditorType(new CKEditorConfiguration(
            (new Processor())->processConfiguration(new Configuration(), [])
        ));

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->ckEditorType)
            ->getFormFactory();

        $this->formType = method_exists(AbstractType::class, 'getBlockPrefix') ? CKEditorType::class : 'ckeditor';
    }

    public function testEnableWithDefaultValue()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('enable', $view->vars);
        $this->assertTrue($view->vars['enable']);
    }

    public function testEnableWithExplicitValue()
    {
        $form = $this->factory->create($this->formType, null, ['enable' => false]);
        $view = $form->createView();

        $this->assertArrayHasKey('enable', $view->vars);
        $this->assertFalse($view->vars['enable']);
    }

    public function testAsyncWithDefaultValue()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('async', $view->vars);
        $this->assertFalse($view->vars['async']);
    }

    public function testAsyncWithExplicitValue()
    {
        $form = $this->factory->create($this->formType, null, ['async' => true]);
        $view = $form->createView();

        $this->assertArrayHasKey('async', $view->vars);
        $this->assertTrue($view->vars['async']);
    }

    public function testAutoloadWithDefaultValue()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('autoload', $view->vars);
        $this->assertTrue($view->vars['autoload']);
    }

    public function testAutoloadWithExplicitValue()
    {
        $form = $this->factory->create($this->formType, null, ['autoload' => false]);
        $view = $form->createView();

        $this->assertArrayHasKey('autoload', $view->vars);
        $this->assertFalse($view->vars['autoload']);
    }

    public function testAutoInlineWithDefaultValue()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('auto_inline', $view->vars);
        $this->assertTrue($view->vars['auto_inline']);
    }

    public function testAutoInlineWithExplicitValue()
    {
        $form = $this->factory->create($this->formType, null, ['auto_inline' => false]);
        $view = $form->createView();

        $this->assertArrayHasKey('auto_inline', $view->vars);
        $this->assertFalse($view->vars['auto_inline']);
    }

    public function testInlineWithDefaultValue()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('inline', $view->vars);
        $this->assertFalse($view->vars['inline']);
    }

    public function testInlineWithExplicitValue()
    {
        $form = $this->factory->create($this->formType, null, ['inline' => true]);
        $view = $form->createView();

        $this->assertArrayHasKey('inline', $view->vars);
        $this->assertTrue($view->vars['inline']);
    }

    public function testJqueryWithDefaultValue()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('jquery', $view->vars);
        $this->assertFalse($view->vars['jquery']);
    }

    public function testJqueryWithExplicitValue()
    {
        $form = $this->factory->create($this->formType, null, ['jquery' => true]);
        $view = $form->createView();

        $this->assertArrayHasKey('jquery', $view->vars);
        $this->assertTrue($view->vars['jquery']);
    }

    public function testInputSyncWithDefaultValue()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('input_sync', $view->vars);
        $this->assertFalse($view->vars['input_sync']);
    }

    public function testInputSyncWithExplicitValue()
    {
        $form = $this->factory->create($this->formType, null, ['input_sync' => true]);
        $view = $form->createView();

        $this->assertArrayHasKey('input_sync', $view->vars);
        $this->assertTrue($view->vars['input_sync']);
    }

    public function testRequireJsWithDefaultValue()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('require_js', $view->vars);
        $this->assertFalse($view->vars['require_js']);
    }

    public function testRequireJsWithExplicitValue()
    {
        $form = $this->factory->create($this->formType, null, ['require_js' => true]);
        $view = $form->createView();

        $this->assertArrayHasKey('require_js', $view->vars);
        $this->assertTrue($view->vars['require_js']);
    }

    public function testFilebrowsersWithDefaultValue()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('filebrowsers', $view->vars);
        $this->assertEmpty($view->vars['filebrowsers']);
    }

    public function testFilebrowsersWithExplicitValue()
    {
        $form = $this->factory->create($this->formType, null, ['filebrowsers' => $filebrowsers = [
            'VideoBrowse',
            'VideoUpload',
        ]]);

        $view = $form->createView();

        $this->assertArrayHasKey('filebrowsers', $view->vars);
        $this->assertSame($filebrowsers, $view->vars['filebrowsers']);
    }

    public function testBaseAndJsPathWithDefaultValues()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('base_path', $view->vars);
        $this->assertSame('bundles/fosckeditor/', $view->vars['base_path']);

        $this->assertArrayHasKey('js_path', $view->vars);
        $this->assertSame('bundles/fosckeditor/ckeditor.js', $view->vars['js_path']);
    }

    public function testBaseAndJsPathWithExplicitValues()
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

    public function testJqueryPathWithDefaultValue()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('jquery_path', $view->vars);
        $this->assertSame('bundles/fosckeditor/adapters/jquery.js', $view->vars['jquery_path']);
    }

    public function testJqueryPathWithExplicitValue()
    {
        $form = $this->factory->create($this->formType, null, ['jquery_path' => 'foo/jquery.js']);
        $view = $form->createView();

        $this->assertArrayHasKey('jquery_path', $view->vars);
        $this->assertSame('foo/jquery.js', $view->vars['jquery_path']);
    }

    public function testDefaultConfig()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertEmpty($view->vars['config']);
    }

    public function testConfigWithExplicitConfig()
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

    public function testDefaultPlugins()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('plugins', $view->vars);
        $this->assertEmpty($view->vars['plugins']);
    }

    public function testPluginsWithExplicitPlugins()
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

    public function testDefaultStylesSet()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertEmpty($view->vars['styles']);
    }

    public function testPluginsWithExplicitStylesSet()
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

    public function testDefaultTemplates()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertEmpty($view->vars['templates']);
    }

    public function testTemplatesWithExplicitTemplates()
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

    public function testExplicitDisable()
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

    /**
     * @expectedException \FOS\CKEditorBundle\Exception\ConfigException
     * @expectedExceptionMessage The CKEditor config "nop" does not exist.
     */
    public function testBadConfig()
    {
        $form = $this->factory->create($this->formType, null, ['config_name' => 'nop']);
        $form->createView();
    }
}
