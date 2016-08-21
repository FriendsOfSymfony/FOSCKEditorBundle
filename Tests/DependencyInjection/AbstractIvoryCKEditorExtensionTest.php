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
use Ivory\CKEditorBundle\Tests\AbstractTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Abstract Ivory CKEditor extension test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
abstract class AbstractIvoryCKEditorExtensionTest extends AbstractTestCase
{
    /** @var \Symfony\Component\DependencyInjection\ContainerBuilder */
    private $container;

    /** @var \Symfony\Component\Asset\Packages|\Symfony\Component\Templating\Helper\CoreAssetsHelper|\PHPUnit_Framework_MockObject_MockObject */
    private $assetsHelperMock;

    /** @var \Symfony\Component\Routing\RouterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $routerMock;

    /** @var \Symfony\Component\Form\FormRendererInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $formRendererMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        if (class_exists('Symfony\Component\Asset\Packages')) {
            $this->assetsHelperMock = $this->getMockBuilder('Symfony\Component\Asset\Packages')
                ->disableOriginalConstructor()
                ->getMock();
        } else {
            $this->assetsHelperMock = $this->getMockBuilder('Symfony\Component\Templating\Helper\CoreAssetsHelper')
                ->disableOriginalConstructor()
                ->getMock();
        }

        $this->routerMock = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $this->formRendererMock = $this->createMock('Symfony\Component\Form\FormRendererInterface');

        $this->container = new ContainerBuilder();

        $this->container->set('assets.packages', $this->assetsHelperMock);
        $this->container->set('router', $this->routerMock);
        $this->container->set('templating.form.renderer', $this->formRendererMock);
        $this->container->set('twig.form.renderer', $this->formRendererMock);

        $this->container->registerExtension($extension = new IvoryCKEditorExtension());
        $this->container->loadFromExtension($extension->getAlias());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->assetsHelperMock);
        unset($this->routerMock);
        unset($this->container);
    }

    /**
     * Loads a configuration.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container     The container.
     * @param string                                                  $configuration The configuration.
     */
    abstract protected function loadConfiguration(ContainerBuilder $container, $configuration);

    public function testFormType()
    {
        $this->container->compile();

        $type = $this->container->get('ivory_ck_editor.form.type');

        $this->assertInstanceOf('Ivory\CKEditorBundle\Form\Type\CKEditorType', $type);
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

        if (Kernel::VERSION_ID < 30000) {
            $this->assertSame(array(array('alias' => 'ckeditor')), $tag);
        } else {
            $this->assertSame(array(array()), $tag);
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

    public function testFilebrowsers()
    {
        $this->loadConfiguration($this->container, 'filebrowsers');
        $this->container->compile();

        $this->assertSame(
            array('VideoBrowse', 'VideoUpload'),
            $this->container->get('ivory_ck_editor.form.type')->getFilebrowsers()
        );
    }

    public function testSingleConfiguration()
    {
        $this->loadConfiguration($this->container, 'single_configuration');
        $this->container->compile();

        $configManager = $this->container->get('ivory_ck_editor.config_manager');

        $expected = array(
            'default' => array(
                'toolbar' => array(
                    array('Source', '-', 'Save'),
                    '/',
                    array('Anchor'),
                    '/',
                    array('Maximize'),
                ),
                'uiColor' => '#000000',
            ),
        );

        $this->assertSame('default', $configManager->getDefaultConfig());
        $this->assertSame($expected, $configManager->getConfigs());
    }

    public function testMultipleConfiguration()
    {
        $this->loadConfiguration($this->container, 'multiple_configuration');
        $this->container->compile();

        $configManager = $this->container->get('ivory_ck_editor.config_manager');

        $expected = array(
            'default' => array(
                'toolbar' => array(
                    array('Source', '-', 'Save'),
                    '/',
                    array('Anchor'),
                    '/',
                    array('Maximize'),
                ),
                'uiColor' => '#000000',
            ),
            'custom' => array(
                'toolbar' => array(
                    array('Source', '-', 'Save'),
                    '/',
                    array('Anchor'),
                ),
                'uiColor' => '#ffffff',
            ),
        );

        $this->assertSame('default', $configManager->getDefaultConfig());
        $this->assertSame($expected, $configManager->getConfigs());
    }

    public function testBasicToolbar()
    {
        $this->loadConfiguration($this->container, 'basic_toolbar');
        $this->container->compile();

        $configManager = $this->container->get('ivory_ck_editor.config_manager');
        $config = $configManager->getConfig('default');

        $this->assertCount(4, $config['toolbar']);
    }

    public function testStandardToolbar()
    {
        $this->loadConfiguration($this->container, 'standard_toolbar');
        $this->container->compile();

        $configManager = $this->container->get('ivory_ck_editor.config_manager');
        $config = $configManager->getConfig('default');

        $this->assertCount(10, $config['toolbar']);
    }

    public function testFullToolbar()
    {
        $this->loadConfiguration($this->container, 'full_toolbar');
        $this->container->compile();

        $configManager = $this->container->get('ivory_ck_editor.config_manager');
        $config = $configManager->getConfig('default');

        $this->assertCount(13, $config['toolbar']);
    }

    public function testPlugins()
    {
        $this->loadConfiguration($this->container, 'plugins');
        $this->container->compile();

        $expected = array('plugin-name' => array(
            'path'     => '/my/path',
            'filename' => 'plugin.js',
        ));

        $this->assertSame($expected, $this->container->get('ivory_ck_editor.plugin_manager')->getPlugins());
    }

    public function testStylesSets()
    {
        $this->loadConfiguration($this->container, 'styles_sets');
        $this->container->compile();

        $expected = array(
            'styles-set-name' => array(
                array(
                    'name'    => 'Blue Title',
                    'element' => 'h2',
                    'styles'  => array('text-decoration' => 'underline'),
                ),
                array(
                    'name'       => 'CSS Style',
                    'element'    => 'span',
                    'attributes' => array('data-class' => 'my-style'),
                ),
                array(
                    'name'       => 'Widget Style',
                    'type'       => 'widget',
                    'widget'     => 'my-widget',
                    'attributes' => array('data-class' => 'my-style'),
                ),
                array(
                    'name'       => 'Multiple Elements Style',
                    'element'    => array('span', 'p', 'h3'),
                    'attributes' => array('data-class' => 'my-style'),
                ),
            ),
        );

        $this->assertSame($expected, $this->container->get('ivory_ck_editor.styles_set_manager')->getStylesSets());
    }

    public function testTemplates()
    {
        $this->loadConfiguration($this->container, 'templates');
        $this->container->compile();

        $expected = array(
            'template-name' => array(
                'imagesPath' => '/my/path',
                'templates'  => array(
                    array(
                        'title'       => 'My Template',
                        'image'       => 'image.jpg',
                        'description' => 'My awesome description',
                        'html'        => '<h1>Template</h1><p>Type your text here.</p>',
                    ),
                ),
            ),
        );

        $this->assertSame($expected, $this->container->get('ivory_ck_editor.template_manager')->getTemplates());
    }

    public function testCustomPaths()
    {
        $this->loadConfiguration($this->container, 'custom_paths');
        $this->container->compile();

        $ckEditorType = $this->container->get('ivory_ck_editor.form.type');

        $this->assertSame('foo/', $ckEditorType->getBasePath());
        $this->assertSame('foo/ckeditor.js', $ckEditorType->getJsPath());
    }

    public function testTemplatingConfiguration()
    {
        $this->container->compile();

        $helper = $this->container->get('ivory_ck_editor.templating.helper');

        $this->assertInstanceOf('Ivory\CKEditorBundle\Templating\CKEditorHelper', $helper);
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

    /**
     * @expectedException \Ivory\CKEditorBundle\Exception\DependencyInjectionException
     * @expectedExceptionMessage The toolbar item "foo" does not exist.
     */
    public function testInvalidToolbarItem()
    {
        $this->loadConfiguration($this->container, 'invalid_toolbar_item');
        $this->container->compile();
    }

    /**
     * @expectedException \Ivory\CKEditorBundle\Exception\DependencyInjectionException
     * @expectedExceptionMessage The toolbar "foo" does not exist.
     */
    public function testInvalidToolbar()
    {
        $this->loadConfiguration($this->container, 'invalid_toolbar');
        $this->container->compile();
    }
}
