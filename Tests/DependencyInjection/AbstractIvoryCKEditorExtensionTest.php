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
use Ivory\CKEditorBundle\Tests\Fixtures\Extension\FrameworkExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Abstract Ivory CKEditor extension test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractIvoryCKEditorExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Symfony\Component\DependencyInjection\ContainerBuilder */
    protected $container;

    /** @var \Symfony\Component\Templating\Helper\CoreAssetsHelper|\PHPUnit_Framework_MockObject_MockObject */
    protected $assetsHelperMock;

    /** @var \Symfony\Component\Routing\RouterInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $routerMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->assetsHelperMock = $this->getMockBuilder('Symfony\Component\Templating\Helper\CoreAssetsHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->routerMock = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $this->container = new ContainerBuilder();

        $this->container->set('templating.helper.assets', $this->assetsHelperMock);
        $this->container->set('router', $this->routerMock);

        $this->container->registerExtension($framework = new FrameworkExtension());
        $this->container->loadFromExtension($framework->getAlias());

        $this->container->registerExtension($ckeditor = new IvoryCKEditorExtension());
        $this->container->loadFromExtension($ckeditor->getAlias());
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

    public function testDefaultFormType()
    {
        $this->container->compile();

        $type = $this->container->get('ivory_ck_editor.form.type');

        $this->assertInstanceOf('Ivory\CKEditorBundle\Form\Type\CKEditorType', $type);
        $this->assertTrue($type->isEnable());
        $this->assertTrue($type->isAutoload());
        $this->assertSame('bundles/ivoryckeditor/', $type->getBasePath());
        $this->assertSame('bundles/ivoryckeditor/ckeditor.js', $type->getJsPath());
        $this->assertSame($this->container->get('ivory_ck_editor.config_manager'), $type->getConfigManager());
        $this->assertSame($this->container->get('ivory_ck_editor.plugin_manager'), $type->getPluginManager());
        $this->assertSame($this->container->get('ivory_ck_editor.styles_set_manager'), $type->getStylesSetManager());
        $this->assertSame($this->container->get('ivory_ck_editor.template_manager'), $type->getTemplateManager());
    }

    public function testTwigResources()
    {
        $this->container->compile();

        $this->assertTrue(
            in_array(
                'IvoryCKEditorBundle:Form:ckeditor_widget.html.twig',
                $this->container->getParameter('twig.form.resources')
            )
        );
    }

    public function testPhpResources()
    {
        $this->container->compile();

        $this->assertTrue(
            in_array('IvoryCKEditorBundle:Form', $this->container->getParameter('templating.helper.form.resources'))
        );
    }

    public function testDisable()
    {
        $this->loadConfiguration($this->container, 'disable');
        $this->container->compile();

        $this->assertFalse($this->container->get('ivory_ck_editor.form.type')->isEnable());
    }

    public function testAutoload()
    {
        $this->loadConfiguration($this->container, 'autoload');
        $this->container->compile();

        $this->assertFalse($this->container->get('ivory_ck_editor.form.type')->isAutoload());
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

        $expected = array('wordcount' => array(
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
            'default' => array(
                array('name' => 'Blue Title', 'element' => 'h2', 'styles' => array('text-decoration' => 'underline')),
                array('name' => 'CSS Style', 'element' => 'span', 'attributes' => array('data-class' => 'my-style')),
            )
        );

        $this->assertSame($expected, $this->container->get('ivory_ck_editor.styles_set_manager')->getStylesSets());
    }

    public function testTemplates()
    {
        $this->loadConfiguration($this->container, 'templates');
        $this->container->compile();

        $expected = array(
            'default' => array(
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

        $this->assertSame('foo', $ckEditorType->getBasePath());
        $this->assertSame('foo/ckeditor.js', $ckEditorType->getJsPath());
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
