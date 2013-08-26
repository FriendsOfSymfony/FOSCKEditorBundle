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
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Scope;

/**
 * Abstract Ivory CKEditor extension test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractIvoryCKEditorExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Symfony\Component\DependencyInjection\ContainerBuilder */
    protected $container;

    /** @var \Symfony\Component\Templating\Helper\CoreAssetsHelper */
    protected $assetsHelperMock;

    /** @var \Symfony\Component\Routing\RouterInterface */
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

        $this->container->addScope(new Scope('request'));

        $this->container->set('templating.helper.assets', $this->assetsHelperMock);
        $this->container->set('router', $this->routerMock);

        $this->container->setParameter('templating.engines', array('php', 'twig'));

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

        $this->container->enterScope('request');

        $this->assertInstanceOf(
            'Ivory\CKEditorBundle\Form\Type\CKEditorType',
            $this->container->get('ivory_ck_editor.form.type')
        );

        $this->container->leaveScope('request');
    }

    public function testAssetsVersionTrimer()
    {
        $this->container->compile();

        $this->assertInstanceOf(
            'Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper',
            $this->container->get('ivory_ck_editor.helper.assets_version_trimer')
        );
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

        $this->container->enterScope('request');

        $this->assertFalse($this->container->get('ivory_ck_editor.form.type')->isEnable());

        $this->container->leaveScope('request');
    }

    public function testSingleConfiguration()
    {
        $this->loadConfiguration($this->container, 'single_configuration');
        $this->container->compile();

        $this->container->enterScope('request');

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

        $this->container->leaveScope('request');
    }

    public function testMultipleConfiguration()
    {
        $this->loadConfiguration($this->container, 'multiple_configuration');
        $this->container->compile();

        $this->container->enterScope('request');

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

        $this->container->leaveScope('request');
    }

    public function testBasicToolbar()
    {
        $this->loadConfiguration($this->container, 'basic_toolbar');
        $this->container->compile();

        $this->container->enterScope('request');

        $configManager = $this->container->get('ivory_ck_editor.config_manager');
        $config = $configManager->getConfig('default');

        $this->assertCount(4, $config['toolbar']);

        $this->container->leaveScope('request');
    }

    public function testStandardToolbar()
    {
        $this->loadConfiguration($this->container, 'standard_toolbar');
        $this->container->compile();

        $this->container->enterScope('request');

        $configManager = $this->container->get('ivory_ck_editor.config_manager');
        $config = $configManager->getConfig('default');

        $this->assertCount(10, $config['toolbar']);

        $this->container->leaveScope('request');
    }

    public function testFullToolbar()
    {
        $this->loadConfiguration($this->container, 'full_toolbar');
        $this->container->compile();

        $this->container->enterScope('request');

        $configManager = $this->container->get('ivory_ck_editor.config_manager');
        $config = $configManager->getConfig('default');

        $this->assertCount(13, $config['toolbar']);

        $this->container->leaveScope('request');
    }

    public function testPlugins()
    {
        $this->loadConfiguration($this->container, 'plugins');
        $this->container->compile();

        $this->container->enterScope('request');

        $this->assetsHelperMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('/my/path'), $this->equalTo(null))
            ->will($this->returnValue('/my/rewritten/path'));

        $pluginManager = $this->container->get('ivory_ck_editor.plugin_manager');

        $expected = array('wordcount' => array(
            'path'     => '/my/rewritten/path',
            'filename' => 'plugin.js',
        ));

        $this->assertSame($expected, $pluginManager->getPlugins());

        $this->container->leaveScope('request');
    }

    public function testCustomPaths()
    {
        $this->loadConfiguration($this->container, 'custom_paths');
        $this->container->compile();

        $this->container->enterScope('request');

        $ckEditorType = $this->container->get('ivory_ck_editor.form.type');

        $this->assertSame('foo', $ckEditorType->getBasePath());
        $this->assertSame('foo/ckeditor.js', $ckEditorType->getJsPath());

        $this->container->leaveScope('request');
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
