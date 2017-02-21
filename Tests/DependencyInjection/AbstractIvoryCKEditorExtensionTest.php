<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests\DependencyInjection;

use Ivory\CKEditorBundle\DependencyInjection\IvoryCKEditorExtension;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Ivory\CKEditorBundle\IvoryCKEditorBundle;
use Ivory\CKEditorBundle\Tests\AbstractTestCase;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\Helper\CoreAssetsHelper;

/**
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
abstract class AbstractIvoryCKEditorExtensionTest extends AbstractTestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var Packages|CoreAssetsHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $packages;

    /**
     * @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $router;

    /**
     * @var FormRendererInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $formRenderer;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->formRenderer = $this->createMock(FormRendererInterface::class);
        $this->packages = $this->getMockBuilder(Packages::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->container = new ContainerBuilder();

        $this->container->set('assets.packages', $this->packages);
        $this->container->set('router', $this->router);
        $this->container->set('templating.form.renderer', $this->formRenderer);
        $this->container->set('twig.form.renderer', $this->formRenderer);

        $this->container->registerExtension($extension = new IvoryCKEditorExtension());
        $this->container->loadFromExtension($extension->getAlias());

        (new IvoryCKEditorBundle())->build($this->container);
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $configuration
     */
    abstract protected function loadConfiguration(ContainerBuilder $container, $configuration);

    public function testFormType()
    {
        $this->container->compile();

        $type = $this->container->get('ivory_ck_editor.form.type');

        $this->assertInstanceOf(CKEditorType::class, $type);
        $this->assertTrue($type->isEnable());
        $this->assertTrue($type->isAutoload());
        $this->assertTrue($type->isAutoInline());
        $this->assertFalse($type->isInline());
        $this->assertFalse($type->useJquery());
        $this->assertFalse($type->isInputSync());
        $this->assertFalse($type->useRequireJs());
        $this->assertFalse($type->hasFilebrowsers());
        $this->assertSame('bundles/ivoryckeditor/', $type->getBasePath());
        $this->assertSame('bundles/ivoryckeditor/ckeditor.js', $type->getJsPath());
        $this->assertSame('bundles/ivoryckeditor/adapters/jquery.js', $type->getJqueryPath());
        $this->assertSame($this->container->get('ivory_ck_editor.config_manager'), $type->getConfigManager());
        $this->assertSame($this->container->get('ivory_ck_editor.plugin_manager'), $type->getPluginManager());
        $this->assertSame($this->container->get('ivory_ck_editor.styles_set_manager'), $type->getStylesSetManager());
        $this->assertSame($this->container->get('ivory_ck_editor.template_manager'), $type->getTemplateManager());
    }

    public function testFormTag()
    {
        $this->container->compile();

        $tag = $this->container->getDefinition('ivory_ck_editor.form.type')->getTag('form.type');

        if (!method_exists(AbstractType::class, 'getBlockPrefix')) {
            $this->assertSame([['alias' => 'ckeditor']], $tag);
        } else {
            $this->assertSame([[]], $tag);
        }
    }

    public function testDisable()
    {
        $this->loadConfiguration($this->container, 'disable');
        $this->container->compile();

        $this->assertFalse($this->container->get('ivory_ck_editor.form.type')->isEnable());
    }

    public function testAsync()
    {
        $this->loadConfiguration($this->container, 'async');
        $this->container->compile();

        $this->assertTrue($this->container->get('ivory_ck_editor.form.type')->isAsync());
    }

    public function testAutoload()
    {
        $this->loadConfiguration($this->container, 'autoload');
        $this->container->compile();

        $this->assertFalse($this->container->get('ivory_ck_editor.form.type')->isAutoload());
    }

    public function testAutoInline()
    {
        $this->loadConfiguration($this->container, 'auto_inline');
        $this->container->compile();

        $this->assertFalse($this->container->get('ivory_ck_editor.form.type')->isAutoInline());
    }

    public function testInline()
    {
        $this->loadConfiguration($this->container, 'inline');
        $this->container->compile();

        $this->assertTrue($this->container->get('ivory_ck_editor.form.type')->isInline());
    }

    public function testInputSync()
    {
        $this->loadConfiguration($this->container, 'input_sync');
        $this->container->compile();

        $this->assertTrue($this->container->get('ivory_ck_editor.form.type')->isInputSync());
    }

    public function testRequireJs()
    {
        $this->loadConfiguration($this->container, 'require_js');
        $this->container->compile();

        $this->assertTrue($this->container->get('ivory_ck_editor.form.type')->useRequireJs());
    }

    public function testJquery()
    {
        $this->loadConfiguration($this->container, 'jquery');
        $this->container->compile();

        $this->assertTrue($this->container->get('ivory_ck_editor.form.type')->useJquery());
    }

    public function testJqueryPath()
    {
        $this->loadConfiguration($this->container, 'jquery_path');
        $this->container->compile();

        $this->assertSame('foo/jquery.js', $this->container->get('ivory_ck_editor.form.type')->getJqueryPath());
    }

    public function testCustomPaths()
    {
        $this->loadConfiguration($this->container, 'custom_paths');
        $this->container->compile();

        $ckEditorType = $this->container->get('ivory_ck_editor.form.type');

        $this->assertSame('foo/', $ckEditorType->getBasePath());
        $this->assertSame('foo/ckeditor.js', $ckEditorType->getJsPath());
    }

    public function testFilebrowsers()
    {
        $this->loadConfiguration($this->container, 'filebrowsers');
        $this->container->compile();

        $this->assertSame(
            ['VideoBrowse', 'VideoUpload'],
            $this->container->get('ivory_ck_editor.form.type')->getFilebrowsers()
        );
    }

    public function testSingleConfiguration()
    {
        $this->loadConfiguration($this->container, 'single_configuration');
        $this->container->compile();

        $configManager = $this->container->get('ivory_ck_editor.config_manager');

        $expected = [
            'default' => [
                'toolbar' => 'default',
                'uiColor' => '#000000',
            ],
        ];

        $this->assertSame('default', $configManager->getDefaultConfig());
        $this->assertSame($expected, $configManager->getConfigs());
    }

    public function testMultipleConfiguration()
    {
        $this->loadConfiguration($this->container, 'multiple_configuration');
        $this->container->compile();

        $configManager = $this->container->get('ivory_ck_editor.config_manager');

        $expected = [
            'default' => [
                'toolbar' => 'default',
                'uiColor' => '#000000',
            ],
            'custom' => [
                'toolbar' => 'custom',
                'uiColor' => '#ffffff',
            ],
        ];

        $this->assertSame('default', $configManager->getDefaultConfig());
        $this->assertSame($expected, $configManager->getConfigs());
    }

    public function testDefaultConfiguration()
    {
        $this->loadConfiguration($this->container, 'default_configuration');
        $this->container->compile();

        $configManager = $this->container->get('ivory_ck_editor.config_manager');

        $expected = [
            'default' => ['uiColor' => '#000000'],
            'custom'  => ['uiColor' => '#ffffff'],
        ];

        $this->assertSame('default', $configManager->getDefaultConfig());
        $this->assertSame($expected, $configManager->getConfigs());
    }

    public function testPlugins()
    {
        $this->loadConfiguration($this->container, 'plugins');
        $this->container->compile();

        $expected = ['plugin-name' => [
            'path'     => '/my/path',
            'filename' => 'plugin.js',
        ]];

        $this->assertSame($expected, $this->container->get('ivory_ck_editor.plugin_manager')->getPlugins());
    }

    public function testStylesSets()
    {
        $this->loadConfiguration($this->container, 'styles_sets');
        $this->container->compile();

        $expected = [
            'styles-set-name' => [
                [
                    'name'    => 'Blue Title',
                    'element' => 'h2',
                    'styles'  => ['text-decoration' => 'underline'],
                ],
                [
                    'name'       => 'CSS Style',
                    'element'    => 'span',
                    'attributes' => ['data-class' => 'my-style'],
                ],
                [
                    'name'       => 'Widget Style',
                    'type'       => 'widget',
                    'widget'     => 'my-widget',
                    'attributes' => ['data-class' => 'my-style'],
                ],
                [
                    'name'       => 'Multiple Elements Style',
                    'element'    => ['span', 'p', 'h3'],
                    'attributes' => ['data-class' => 'my-style'],
                ],
            ],
        ];

        $this->assertSame($expected, $this->container->get('ivory_ck_editor.styles_set_manager')->getStylesSets());
    }

    public function testTemplates()
    {
        $this->loadConfiguration($this->container, 'templates');
        $this->container->compile();

        $expected = [
            'template-name' => [
                'imagesPath' => '/my/path',
                'templates'  => [
                    [
                        'title'               => 'My Template',
                        'image'               => 'image.jpg',
                        'description'         => 'My awesome description',
                        'html'                => '<h1>Template</h1><p>Type your text here.</p>',
                        'template'            => 'AppBundle:CKEditor:template.html.twig',
                        'template_parameters' => ['foo' => 'bar'],
                    ],
                ],
            ],
        ];

        $this->assertSame($expected, $this->container->get('ivory_ck_editor.template_manager')->getTemplates());
    }

    public function testToolbars()
    {
        $this->loadConfiguration($this->container, 'toolbars');
        $this->container->compile();

        $toolbarManager = $this->container->get('ivory_ck_editor.toolbar_manager');

        $this->assertSame(
            [
                'document' => ['Source', '-', 'Save'],
                'tools'    => ['Maximize'],
            ],
            array_intersect_key($toolbarManager->getItems(), ['document' => true, 'tools' => true])
        );

        $this->assertSame(
            [
                'default' => [
                    '@document',
                    '/',
                    ['Anchor'],
                    '/',
                    '@tools',
                ],
                'custom' => [
                    '@document',
                    '/',
                    ['Anchor'],
                ],
            ],
            array_intersect_key($toolbarManager->getToolbars(), ['default' => true, 'custom' => true])
        );
    }

    /**
     * @expectedException \Ivory\CKEditorBundle\Exception\DependencyInjectionException
     * @expectedExceptionMessage The default config "bar" does not exist.
     */
    public function testInvalidDefaultConfig()
    {
        $this->loadConfiguration($this->container, 'invalid_default_config');
        $this->container->compile();
    }
}
