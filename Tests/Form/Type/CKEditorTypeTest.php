<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests\Form\Type;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Ivory\CKEditorBundle\Model\ConfigManagerInterface;
use Ivory\CKEditorBundle\Model\PluginManagerInterface;
use Ivory\CKEditorBundle\Model\StylesSetManagerInterface;
use Ivory\CKEditorBundle\Model\TemplateManagerInterface;
use Ivory\CKEditorBundle\Model\ToolbarManagerInterface;
use Ivory\CKEditorBundle\Tests\AbstractTestCase;
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
     * @var ConfigManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configManager;

    /**
     * @var PluginManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $pluginManager;

    /**
     * @var StylesSetManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stylesSetManager;

    /**
     * @var TemplateManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $templateManager;

    /**
     * @var ToolbarManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $toolbarManager;

    /**
     * {@inheritdooc}.
     */
    protected function setUp()
    {
        $this->configManager = $this->createMock(ConfigManagerInterface::class);
        $this->pluginManager = $this->createMock(PluginManagerInterface::class);
        $this->stylesSetManager = $this->createMock(StylesSetManagerInterface::class);
        $this->templateManager = $this->createMock(TemplateManagerInterface::class);
        $this->toolbarManager = $this->createMock(ToolbarManagerInterface::class);

        $this->ckEditorType = new CKEditorType(
            $this->configManager,
            $this->pluginManager,
            $this->stylesSetManager,
            $this->templateManager,
            $this->toolbarManager
        );

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->ckEditorType)
            ->getFormFactory();

        $this->formType = method_exists(AbstractType::class, 'getBlockPrefix') ? CKEditorType::class : 'ckeditor';
    }

    public function testInitialState()
    {
        $this->assertTrue($this->ckEditorType->isEnable());
        $this->assertFalse($this->ckEditorType->isAsync());
        $this->assertTrue($this->ckEditorType->isAutoload());
        $this->assertTrue($this->ckEditorType->isAutoInline());
        $this->assertFalse($this->ckEditorType->isInline());
        $this->assertFalse($this->ckEditorType->useJquery());
        $this->assertFalse($this->ckEditorType->isInputSync());
        $this->assertFalse($this->ckEditorType->useRequireJs());
        $this->assertFalse($this->ckEditorType->hasFilebrowsers());
        $this->assertSame('bundles/ivoryckeditor/', $this->ckEditorType->getBasePath());
        $this->assertSame('bundles/ivoryckeditor/ckeditor.js', $this->ckEditorType->getJsPath());
        $this->assertSame('bundles/ivoryckeditor/adapters/jquery.js', $this->ckEditorType->getJqueryPath());
        $this->assertSame($this->configManager, $this->ckEditorType->getConfigManager());
        $this->assertSame($this->pluginManager, $this->ckEditorType->getPluginManager());
        $this->assertSame($this->stylesSetManager, $this->ckEditorType->getStylesSetManager());
        $this->assertSame($this->templateManager, $this->ckEditorType->getTemplateManager());
        $this->assertSame($this->toolbarManager, $this->ckEditorType->getToolbarManager());
    }

    public function testSetFilebrowsers()
    {
        $this->ckEditorType->setFilebrowsers($filebrowsers = [
            'VideoBrowse',
            'VideoUpload',
        ]);

        $this->assertTrue($this->ckEditorType->hasFilebrowsers());
        $this->assertSame($filebrowsers, $this->ckEditorType->getFilebrowsers());

        foreach ($filebrowsers as $filebrowser) {
            $this->assertTrue($this->ckEditorType->hasFilebrowser($filebrowser));
        }
    }

    public function testAddFilebrowser()
    {
        $this->ckEditorType->addFilebrowser($filebrowser = 'VideoBrowse');

        $this->assertTrue($this->ckEditorType->hasFilebrowsers());
        $this->assertSame([$filebrowser], $this->ckEditorType->getFilebrowsers());
        $this->assertTrue($this->ckEditorType->hasFilebrowser($filebrowser));
    }

    public function testRemoveFilebrowser()
    {
        $this->ckEditorType->addFilebrowser($filebrowser = 'VideoBrowse');
        $this->ckEditorType->removeFilebrowser($filebrowser);

        $this->assertFalse($this->ckEditorType->hasFilebrowsers());
        $this->assertEmpty($this->ckEditorType->getFilebrowsers());
        $this->assertFalse($this->ckEditorType->hasFilebrowser($filebrowser));
    }

    public function testEnableWithDefaultValue()
    {
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('enable', $view->vars);
        $this->assertTrue($view->vars['enable']);
    }

    public function testEnableWithConfiguredValue()
    {
        $this->ckEditorType->isEnable(false);

        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('enable', $view->vars);
        $this->assertFalse($view->vars['enable']);
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

    public function testAsyncWithConfiguredValue()
    {
        $this->ckEditorType->isAsync(true);

        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('async', $view->vars);
        $this->assertTrue($view->vars['async']);
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

    public function testAutoloadWithConfiguredValue()
    {
        $this->ckEditorType->isAutoload(false);

        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('autoload', $view->vars);
        $this->assertFalse($view->vars['autoload']);
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

    public function testAutoInlineWithConfiguredValue()
    {
        $this->ckEditorType->isAutoInline(false);

        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('auto_inline', $view->vars);
        $this->assertFalse($view->vars['auto_inline']);
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

    public function testInlineWithConfiguredValue()
    {
        $this->ckEditorType->isInline(true);

        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('inline', $view->vars);
        $this->assertTrue($view->vars['inline']);
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

    public function testJqueryWithConfiguredValue()
    {
        $this->ckEditorType->useJquery(true);

        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('jquery', $view->vars);
        $this->assertTrue($view->vars['jquery']);
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

    public function testInputSyncWithConfiguredValue()
    {
        $this->ckEditorType->isInputSync(true);

        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('input_sync', $view->vars);
        $this->assertTrue($view->vars['input_sync']);
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

    public function testRequireJsWithConfiguredValue()
    {
        $this->ckEditorType->useRequireJs(true);

        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('require_js', $view->vars);
        $this->assertTrue($view->vars['require_js']);
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

    public function testFilebrowsersWithConfiguredValue()
    {
        $this->ckEditorType->setFilebrowsers($filebrowsers = [
            'VideoBrowser',
            'VideoUpload',
        ]);

        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('filebrowsers', $view->vars);
        $this->assertSame($filebrowsers, $view->vars['filebrowsers']);
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
        $this->assertSame('bundles/ivoryckeditor/', $view->vars['base_path']);

        $this->assertArrayHasKey('js_path', $view->vars);
        $this->assertSame('bundles/ivoryckeditor/ckeditor.js', $view->vars['js_path']);
    }

    public function testBaseAndJsPathWithConfiguredValues()
    {
        $this->ckEditorType->setBasePath('foo/base/');
        $this->ckEditorType->setJsPath('foo/ckeditor.js');
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('base_path', $view->vars);
        $this->assertSame('foo/base/', $view->vars['base_path']);

        $this->assertArrayHasKey('js_path', $view->vars);
        $this->assertSame('foo/ckeditor.js', $view->vars['js_path']);
    }

    public function testBaseAndJsPathWithExplicitValues()
    {
        $form = $this->factory->create(
            $this->formType,
            null,
            [
                'base_path' => 'foo',
                'js_path'   => 'foo/ckeditor.js',
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
        $this->assertSame('bundles/ivoryckeditor/adapters/jquery.js', $view->vars['jquery_path']);
    }

    public function testJqueryPathWithConfiguredValue()
    {
        $this->ckEditorType->setJqueryPath('foo/jquery.js');
        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('jquery_path', $view->vars);
        $this->assertSame('foo/jquery.js', $view->vars['jquery_path']);
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
        $this->assertEmpty(json_decode($view->vars['config'], true));
    }

    public function testConfigWithExplicitConfig()
    {
        $options = [
            'config' => [
                'toolbar' => ['foo' => 'bar'],
                'uiColor' => '#ffffff',
            ],
        ];

        $this->configManager
            ->expects($this->once())
            ->method('setConfig')
            ->with($this->anything(), $this->equalTo($options['config']));

        $this->configManager
            ->expects($this->once())
            ->method('getConfig')
            ->with($this->anything())
            ->will($this->returnValue($options['config']));

        $form = $this->factory->create($this->formType, null, $options);
        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertSame($options['config'], $view->vars['config']);
    }

    public function testConfigWithConfiguredConfig()
    {
        $config = [
            'toolbar' => 'default',
            'uiColor' => '#ffffff',
        ];

        $this->configManager
            ->expects($this->once())
            ->method('mergeConfig')
            ->with($this->equalTo('default'), $this->equalTo([]));

        $this->configManager
            ->expects($this->once())
            ->method('getConfig')
            ->with($this->identicalTo('default'))
            ->will($this->returnValue($config));

        $this->toolbarManager
            ->expects($this->once())
            ->method('resolveToolbar')
            ->with($this->identicalTo('default'))
            ->will($this->returnValue($config['toolbar'] = ['foo' => 'bar']));

        $form = $this->factory->create($this->formType, null, ['config_name' => 'default']);
        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertSame($config, $view->vars['config']);
    }

    public function testConfigWithDefaultConfiguredConfig()
    {
        $options = [
            'toolbar' => ['foo' => 'bar'],
            'uiColor' => '#ffffff',
        ];

        $this->configManager
            ->expects($this->once())
            ->method('getDefaultConfig')
            ->will($this->returnValue('config'));

        $this->configManager
            ->expects($this->once())
            ->method('mergeConfig')
            ->with($this->equalTo('config'), $this->equalTo([]));

        $this->configManager
            ->expects($this->once())
            ->method('getConfig')
            ->with('config')
            ->will($this->returnValue($options));

        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertSame($options, $view->vars['config']);
    }

    public function testConfigWithExplicitAndConfiguredConfig()
    {
        $configuredConfig = [
            'toolbar' => 'default',
            'uiColor' => '#ffffff',
        ];

        $explicitConfig = ['uiColor' => '#000000'];

        $this->configManager
            ->expects($this->once())
            ->method('mergeConfig')
            ->with($this->equalTo('default'), $this->equalTo($explicitConfig));

        $this->configManager
            ->expects($this->once())
            ->method('getConfig')
            ->with('default')
            ->will($this->returnValue(array_merge($configuredConfig, $explicitConfig)));

        $this->toolbarManager
            ->expects($this->once())
            ->method('resolveToolbar')
            ->with($this->identicalTo('default'))
            ->will($this->returnValue($configuredConfig['toolbar'] = ['foo' => 'bar']));

        $form = $this->factory->create(
            $this->formType,
            null,
            ['config_name' => 'default', 'config' => $explicitConfig]
        );

        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertSame(array_merge($configuredConfig, $explicitConfig), $view->vars['config']);
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
                'path'     => '/my/path',
                'filename' => 'plugin.js',
            ],
        ];

        $this->pluginManager
            ->expects($this->once())
            ->method('setPlugins')
            ->with($this->equalTo($plugins));

        $this->pluginManager
            ->expects($this->once())
            ->method('getPlugins')
            ->will($this->returnValue($plugins));

        $form = $this->factory->create($this->formType, null, ['plugins' => $plugins]);

        $view = $form->createView();

        $this->assertArrayHasKey('plugins', $view->vars);
        $this->assertSame($plugins, $view->vars['plugins']);
    }

    public function testPluginsWithConfiguredPlugins()
    {
        $plugins = [
            'wordcount' => [
                'path'     => '/my/path',
                'filename' => 'plugin.js',
            ],
        ];

        $this->pluginManager
            ->expects($this->once())
            ->method('getPlugins')
            ->will($this->returnValue($plugins));

        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertArrayHasKey('plugins', $view->vars);
        $this->assertSame($plugins, $view->vars['plugins']);
    }

    public function testPluginsWithConfiguredAndExplicitPlugins()
    {
        $configuredPlugins = [
            'wordcount' => [
                'path'     => '/my/explicit/path',
                'filename' => 'plugin.js',
            ],
        ];

        $explicitPlugins = [
            'autogrow' => [
                'path'     => '/my/configured/path',
                'filename' => 'plugin.js',
            ],
        ];

        $this->pluginManager
            ->expects($this->once())
            ->method('setPlugins')
            ->with($this->equalTo($explicitPlugins));

        $this->pluginManager
            ->expects($this->once())
            ->method('getPlugins')
            ->will($this->returnValue(array_merge($explicitPlugins, $configuredPlugins)));

        $form = $this->factory->create($this->formType, null, ['plugins' => $explicitPlugins]);
        $view = $form->createView();

        $this->assertArrayHasKey('plugins', $view->vars);
        $this->assertSame(array_merge($explicitPlugins, $configuredPlugins), $view->vars['plugins']);
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

        $this->stylesSetManager
            ->expects($this->once())
            ->method('setStylesSets')
            ->with($this->equalTo($stylesSets));

        $this->stylesSetManager
            ->expects($this->once())
            ->method('getStylesSets')
            ->will($this->returnValue($stylesSets));

        $form = $this->factory->create($this->formType, null, ['styles' => $stylesSets]);

        $view = $form->createView();

        $this->assertSame($stylesSets, $view->vars['styles']);
    }

    public function testPluginsWithConfiguredStylesSets()
    {
        $stylesSets = [
            'default' => [
                ['name' => 'Blue Title', 'element' => 'h2', 'styles' => ['color' => 'Blue']],
                ['name' => 'CSS Style', 'element' => 'span', 'attributes' => ['class' => 'my_style']],
            ],
        ];

        $this->stylesSetManager
            ->expects($this->once())
            ->method('getStylesSets')
            ->will($this->returnValue($stylesSets));

        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertSame($stylesSets, $view->vars['styles']);
    }

    public function testPluginsWithConfiguredAndExplicitStylesSets()
    {
        $configuredStylesSets = [
            'foo' => [
                ['name' => 'Blue Title', 'element' => 'h2', 'styles' => ['color' => 'Blue']],
            ],
        ];

        $explicitStylesSets = [
            'bar' => [
                ['name' => 'CSS Style', 'element' => 'span', 'attributes' => ['class' => 'my_style']],
            ],
        ];

        $this->stylesSetManager
            ->expects($this->once())
            ->method('setStylesSets')
            ->with($this->equalTo($explicitStylesSets));

        $this->stylesSetManager
            ->expects($this->once())
            ->method('getStylesSets')
            ->will($this->returnValue(array_merge($explicitStylesSets, $configuredStylesSets)));

        $form = $this->factory->create($this->formType, null, ['styles' => $explicitStylesSets]);
        $view = $form->createView();

        $this->assertSame(array_merge($explicitStylesSets, $configuredStylesSets), $view->vars['styles']);
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
                'templates'  => [
                    [
                        'title' => 'My Template',
                        'html'  => '<h1>Template</h1><p>Type your text here.</p>',
                    ],
                ],
            ],
        ];

        $this->templateManager
            ->expects($this->once())
            ->method('setTemplates')
            ->with($this->equalTo($templates));

        $this->templateManager
            ->expects($this->once())
            ->method('getTemplates')
            ->will($this->returnValue($templates));

        $form = $this->factory->create($this->formType, null, ['templates' => $templates]);

        $view = $form->createView();

        $this->assertSame($templates, $view->vars['templates']);
    }

    public function testTemplatesWithConfiguredTemplates()
    {
        $templates = [
            'default' => [
                'imagesPath' => '/my/path',
                'templates'  => [
                    [
                        'title' => 'My Template',
                        'html'  => '<h1>Template</h1><p>Type your text here.</p>',
                    ],
                ],
            ],
        ];

        $this->templateManager
            ->expects($this->once())
            ->method('getTemplates')
            ->will($this->returnValue($templates));

        $form = $this->factory->create($this->formType);
        $view = $form->createView();

        $this->assertSame($templates, $view->vars['templates']);
    }

    public function testTemplatesWithConfiguredAndExplicitTemplates()
    {
        $configuredTemplates = [
            'default' => [
                'imagesPath' => '/my/path',
                'templates'  => [
                    [
                        'title' => 'My Template',
                        'html'  => '<h1>Template</h1><p>Type your text here.</p>',
                    ],
                ],
            ],
        ];

        $explicitTemplates = [
            'extra' => [
                'templates'  => [
                    [
                        'title' => 'My Extra Template',
                        'html'  => '<h2>Template</h2><p>Type your text here.</p>',
                    ],
                ],
            ],
        ];

        $this->templateManager
            ->expects($this->once())
            ->method('setTemplates')
            ->with($this->equalTo($explicitTemplates));

        $this->templateManager
            ->expects($this->once())
            ->method('getTemplates')
            ->will($this->returnValue(array_merge($explicitTemplates, $configuredTemplates)));

        $form = $this->factory->create($this->formType, null, ['templates' => $explicitTemplates]);
        $view = $form->createView();

        $this->assertSame(array_merge($explicitTemplates, $configuredTemplates), $view->vars['templates']);
    }

    public function testConfiguredDisable()
    {
        $this->ckEditorType->isEnable(false);

        $options = [
            'config' => [
                'toolbar' => ['foo' => 'bar'],
                'uiColor' => '#ffffff',
            ],
            'plugins' => [
                'wordcount' => [
                    'path'     => '/my/path',
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
                    'path'     => '/my/path',
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
}
