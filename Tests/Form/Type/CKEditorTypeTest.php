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
use Symfony\Component\Form\Forms;

/**
 * CKEditor type test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Symfony\Component\Form\FormFactoryInterface */
    protected $factory;

    /** @var \Ivory\CKEditorBundle\Form\Type\CKEditorType */
    protected $ckEditorType;

    /** @var \Ivory\CKEditorBundle\Model\ConfigManager */
    protected $configManagerMock;

    /** @var \Ivory\CKEditorBundle\Model\PluginManager */
    protected $pluginManagerMock;

    /** @var \Symfony\Component\Templating\Helper\CoreAssetsHelper */
    protected $assetsHelperMock;

    /** @var \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper */
    protected $assetsVersionTrimerHelperMock;

    /**
     * {@inheritdooc}
     */
    protected function setUp()
    {
        $this->configManagerMock = $this->getMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');
        $this->pluginManagerMock = $this->getMock('Ivory\CKEditorBundle\Model\PluginManagerInterface');

        $this->assetsHelperMock = $this->getMockBuilder('Symfony\Component\Templating\Helper\CoreAssetsHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assetsVersionTrimerHelperMock = $this->getMock('Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper');

        $this->ckEditorType = new CKEditorType(
            true,
            'bundles/ckeditor/',
            'bundles/ckeditor/ckeditor.js',
            $this->configManagerMock,
            $this->pluginManagerMock,
            $this->assetsHelperMock,
            $this->assetsVersionTrimerHelperMock
        );

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->ckEditorType)
            ->getFormFactory();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->assetsVersionTrimerHelperMock);
        unset($this->assetsHelperMock);
        unset($this->configManagerMock);
        unset($this->pluginManagerMock);
        unset($this->ckEditorType);
        unset($this->factory);
    }

    public function testInitialState()
    {
        $this->assertTrue($this->ckEditorType->isEnable());
        $this->assertSame('bundles/ckeditor/', $this->ckEditorType->getBasePath());
        $this->assertSame('bundles/ckeditor/ckeditor.js', $this->ckEditorType->getJsPath());
        $this->assertSame($this->configManagerMock, $this->ckEditorType->getConfigManager());
        $this->assertSame($this->pluginManagerMock, $this->ckEditorType->getPluginManager());
        $this->assertSame($this->assetsHelperMock, $this->ckEditorType->getAssetsHelper());
        $this->assertSame($this->assetsVersionTrimerHelperMock, $this->ckEditorType->getAssetsVersionTrimerHelper());
    }

    public function testDefaultRequired()
    {
        $form = $this->factory->create('ckeditor');
        $view = $form->createView();

        $this->assertArrayHasKey('required', $view->vars);
        $this->assertFalse($view->vars['required']);
    }

    /**
     * There is a know bug in CKEditor which makes it unusable with the required HTML5 placeholder.
     *
     * @link http://dev.ckeditor.com/ticket/8031.
     *
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testRequired()
    {
        $this->factory->create('ckeditor', null, array('required' => true));
    }

    public function testBaseAndJsPathWithConfiguredValues()
    {
        $this->assetsHelperMock
            ->expects($this->any())
            ->method('getUrl')
            ->will(
                $this->returnValueMap(
                    array(
                        array('bundles/ckeditor/', null, '/bundles/ckeditor/?v=1'),
                        array('bundles/ckeditor/ckeditor.js', null, '/bundles/ckeditor/ckeditor.js?v=1')
                    )
                )
            );

        $this->assetsVersionTrimerHelperMock
            ->expects($this->once())
            ->method('trim')
            ->with($this->equalTo('/bundles/ckeditor/?v=1'))
            ->will($this->returnValue('/bundles/ckeditor/'));

        $form = $this->factory->create('ckeditor');
        $view = $form->createView();

        $this->assertArrayHasKey('base_path', $view->vars);
        $this->assertSame('/bundles/ckeditor/', $view->vars['base_path']);

        $this->assertArrayHasKey('js_path', $view->vars);
        $this->assertSame('/bundles/ckeditor/ckeditor.js?v=1', $view->vars['js_path']);
    }

    public function testBaseAndJsPathWithConfiguredAndExplicitValues()
    {
        $this->assetsHelperMock
            ->expects($this->any())
            ->method('getUrl')
            ->will(
                $this->returnValueMap(
                    array(
                        array('foo/', null, '/foo/?v=1'),
                        array('foo/ckeditor.js', null, '/foo/ckeditor.js?v=1')
                    )
                )
            );

        $this->assetsVersionTrimerHelperMock
            ->expects($this->once())
            ->method('trim')
            ->with($this->equalTo('/foo/?v=1'))
            ->will($this->returnValue('/foo/'));

        $form = $this->factory->create(
            'ckeditor',
            null,
            array('base_path' => 'foo/', 'js_path' => 'foo/ckeditor.js')
        );

        $view = $form->createView();

        $this->assertArrayHasKey('base_path', $view->vars);
        $this->assertSame('/foo/', $view->vars['base_path']);

        $this->assertArrayHasKey('js_path', $view->vars);
        $this->assertSame('/foo/ckeditor.js?v=1', $view->vars['js_path']);
    }

    public function testDefaultConfig()
    {
        $form = $this->factory->create('ckeditor');
        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertEmpty(json_decode($view->vars['config'], true));
    }

    public function testConfigWithExplicitConfig()
    {
        $options = array(
            'config' => array(
                'toolbar'  => array('foo' => 'bar'),
                'uiColor' => '#ffffff',
            ),
        );

        $this->configManagerMock
            ->expects($this->once())
            ->method('setConfig')
            ->with($this->anything(), $this->equalTo($options['config']));

        $this->configManagerMock
            ->expects($this->once())
            ->method('getConfig')
            ->with($this->anything())
            ->will($this->returnValue($options['config']));

        $form = $this->factory->create('ckeditor', null, $options);
        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertSame($options['config'], json_decode($view->vars['config'], true));
    }

    public function testConfigWithConfiguredConfig()
    {
        $config = array(
            'toolbar'  => 'default',
            'uiColor' => '#ffffff',
        );

        $this->configManagerMock
            ->expects($this->once())
            ->method('mergeConfig')
            ->with($this->equalTo('default'), $this->equalTo(array()));

        $this->configManagerMock
            ->expects($this->once())
            ->method('getConfig')
            ->with('default')
            ->will($this->returnValue($config));

        $form = $this->factory->create('ckeditor', null, array('config_name' => 'default'));
        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertSame($config, json_decode($view->vars['config'], true));
    }

    public function testConfigWithDefaultConfiguredConfig()
    {
        $options = array(
            'toolbar'  => array('foo' => 'bar'),
            'uiColor' => '#ffffff',
        );

        $this->configManagerMock
            ->expects($this->once())
            ->method('getDefaultConfig')
            ->will($this->returnValue('config'));

        $this->configManagerMock
            ->expects($this->once())
            ->method('mergeConfig')
            ->with($this->equalTo('config'), $this->equalTo(array()));

        $this->configManagerMock
            ->expects($this->once())
            ->method('getConfig')
            ->with('config')
            ->will($this->returnValue($options));

        $form = $this->factory->create('ckeditor');
        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertSame($options, json_decode($view->vars['config'], true));
    }

    public function testConfigWithExplicitAndConfiguredConfig()
    {
        $configuredConfig = array(
            'toolbar'  => 'default',
            'uiColor' => '#ffffff',
        );

        $explicitConfig = array('uiColor' => '#000000');

        $this->configManagerMock
            ->expects($this->once())
            ->method('mergeConfig')
            ->with($this->equalTo('default'), $this->equalTo($explicitConfig));

        $this->configManagerMock
            ->expects($this->once())
            ->method('getConfig')
            ->with('default')
            ->will($this->returnValue(array_merge($configuredConfig, $explicitConfig)));

        $form = $this->factory->create(
            'ckeditor',
            null,
            array('config_name' => 'default', 'config' => $explicitConfig)
        );

        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertSame(array_merge($configuredConfig, $explicitConfig), json_decode($view->vars['config'], true));
    }

    public function testDefaultPlugins()
    {
        $form = $this->factory->create('ckeditor');
        $view = $form->createView();

        $this->assertArrayHasKey('plugins', $view->vars);
        $this->assertEmpty($view->vars['plugins']);
    }

    public function testPluginsWithExplicitPlugins()
    {
        $plugins = array(
            'wordcount' => array(
                'path'     => '/my/path',
                'filename' => 'plugin.js',
            ),
        );

        $this->pluginManagerMock
            ->expects($this->once())
            ->method('setPlugins')
            ->with($this->equalTo($plugins));

        $this->pluginManagerMock
            ->expects($this->once())
            ->method('getPlugins')
            ->will($this->returnValue($plugins));

        $form = $this->factory->create('ckeditor', null, array('plugins' => $plugins));

        $view = $form->createView();

        $this->assertArrayHasKey('plugins', $view->vars);
        $this->assertSame($plugins, $view->vars['plugins']);
    }

    public function testPluginsWithConfiguredPlugins()
    {
        $plugins = array(
            'wordcount' => array(
                'path'     => '/my/path',
                'filename' => 'plugin.js',
            ),
        );

        $this->pluginManagerMock
            ->expects($this->once())
            ->method('getPlugins')
            ->will($this->returnValue($plugins));

        $form = $this->factory->create('ckeditor');
        $view = $form->createView();

        $this->assertArrayHasKey('plugins', $view->vars);
        $this->assertSame($plugins, $view->vars['plugins']);
    }

    public function testPluginsWithConfiguredAndExplicitPlugins()
    {
        $configuredPlugins = array(
            'wordcount' => array(
                'path'     => '/my/explicit/path',
                'filename' => 'plugin.js',
            ),
        );

        $explicitPlugins = array(
            'autogrow' => array(
                'path'     => '/my/configured/path',
                'filename' => 'plugin.js',
            ),
        );

        $this->pluginManagerMock
            ->expects($this->once())
            ->method('setPlugins')
            ->with($this->equalTo($explicitPlugins));

        $this->pluginManagerMock
            ->expects($this->once())
            ->method('getPlugins')
            ->will($this->returnValue(array_merge($explicitPlugins, $configuredPlugins)));

        $form = $this->factory->create('ckeditor', null, array('plugins' => $explicitPlugins));
        $view = $form->createView();

        $this->assertArrayHasKey('plugins', $view->vars);
        $this->assertSame(array_merge($explicitPlugins, $configuredPlugins), $view->vars['plugins']);
    }

    public function testConfiguredDisable()
    {
        $this->ckEditorType->isEnable(false);

        $options = array(
            'config' => array(
                'toolbar'  => array('foo' => 'bar'),
                'uiColor' => '#ffffff',
            ),
            'plugins' => array(
                'wordcount' => array(
                    'path'     => '/my/path',
                    'filename' => 'plugin.js',
                ),
            ),
        );

        $form = $this->factory->create('ckeditor', null, $options);
        $view = $form->createView();

        $this->assertArrayHasKey('enable', $view->vars);
        $this->assertFalse($view->vars['enable']);

        $this->assertArrayNotHasKey('config', $view->vars);
        $this->assertArrayNotHasKey('plugins', $view->vars);
    }

    public function testExplicitDisable()
    {
        $options = array(
            'enable' => false,
            'config' => array(
                'toolbar'  => array('foo' => 'bar'),
                'uiColor' => '#ffffff',
            ),
            'plugins' => array(
                'wordcount' => array(
                    'path'     => '/my/path',
                    'filename' => 'plugin.js',
                ),
            ),
        );

        $form = $this->factory->create('ckeditor', null, $options);
        $view = $form->createView();

        $this->assertArrayHasKey('enable', $view->vars);
        $this->assertFalse($view->vars['enable']);

        $this->assertArrayNotHasKey('config', $view->vars);
        $this->assertArrayNotHasKey('plugins', $view->vars);
    }
}
