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

use Ivory\CKEditorBundle\Form\Type\CKEditorType,
    Ivory\CKEditorBundle\Model\ConfigManager,
    Ivory\CKEditorBundle\Model\PluginManager,
    Symfony\Component\Form\Forms;

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
    protected $configManager;

    /** @var \Ivory\CKEditorBundle\Model\PluginManager */
    protected $pluginManager;

    /** @var \Symfony\Component\Templating\Helper\CoreAssetsHelper */
    protected $assetsHelperMock;

    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $routerMock;

    /**
     * {@inheritdooc}
     */
    protected function setUp()
    {
        $this->assetsHelperMock = $this->getMockBuilder('Symfony\Component\Templating\Helper\CoreAssetsHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->routerMock = $this->getMock('Symfony\Component\Routing\RouterInterface');

        $this->configManager = new ConfigManager($this->assetsHelperMock, $this->routerMock);
        $this->pluginManager = new PluginManager($this->assetsHelperMock);

        $this->ckEditorType = new CKEditorType(true, $this->configManager, $this->pluginManager);

        $this->factory = Forms::createFormFactoryBuilder()
            ->addType($this->ckEditorType)
            ->getFormFactory();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->routerMock);
        unset($this->assetsHelperMock);
        unset($this->configManager);
        unset($this->pluginManager);
        unset($this->ckEditorType);
        unset($this->factory);
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

    public function testDefaultConfig()
    {
        $form = $this->factory->create('ckeditor');
        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertEmpty($view->vars['config']);
    }

    public function testConfigWithExplicitConfig()
    {
        $options = array(
            'config' => array(
                'toolbar'  => array('foo' => 'bar'),
                'ui_color' => '#ffffff',
            ),
        );

        $form = $this->factory->create('ckeditor', null, $options);
        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertSame($options['config'], $view->vars['config']);
    }

    public function testConfigWithConfiguredConfig()
    {
        $config = array(
            'toolbar'  => 'default',
            'ui_color' => '#ffffff',
        );

        $this->configManager->setConfig('default', $config);

        $form = $this->factory->create('ckeditor', null, array('config_name' => 'default'));
        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertSame($config, $view->vars['config']);
    }

    public function testConfigWithExplicitAndConfiguredConfig()
    {
        $configuredConfig = array(
            'toolbar'  => 'default',
            'ui_color' => '#ffffff',
        );

        $explicitConfig = array('ui_color' => '#000000');

        $this->configManager->setConfig('default', $configuredConfig);

        $form = $this->factory->create('ckeditor', null, array(
            'config_name' => 'default',
            'config'      => $explicitConfig,
        ));

        $view = $form->createView();

        $this->assertArrayHasKey('config', $view->vars);
        $this->assertSame(array_merge($configuredConfig, $explicitConfig), $view->vars['config']);
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
        $this->assetsHelperMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('/my/path'), $this->equalTo(null))
            ->will($this->returnValue('/my/rewritten/path'));

        $form = $this->factory->create('ckeditor', null, array(
            'plugins' => array(
                'wordcount' => array(
                    'path'     => '/my/path',
                    'filename' => 'plugin.js',
                )),
            )
        );

        $view = $form->createView();

        $this->assertArrayHasKey('plugins', $view->vars);
        $this->assertSame(
            array('wordcount' => array('path' => '/my/rewritten/path', 'filename' => 'plugin.js')),
            $view->vars['plugins']
        );
    }

    public function testPluginsWithConfiguredPlugins()
    {
        $this->assetsHelperMock
            ->expects($this->once())
            ->method('getUrl')
            ->with($this->equalTo('/my/path'), $this->equalTo(null))
            ->will($this->returnValue('/my/rewritten/path'));

        $this->pluginManager->setPlugins(array(
            'wordcount' => array(
                'path'     => '/my/path',
                'filename' => 'plugin.js',
            ),
        ));

        $form = $this->factory->create('ckeditor');
        $view = $form->createView();

        $this->assertArrayHasKey('plugins', $view->vars);
        $this->assertSame(
            array('wordcount' => array('path' => '/my/rewritten/path', 'filename' => 'plugin.js')),
            $view->vars['plugins']
        );
    }

    public function testPluginsWithConfiguredAndExplicitPlugins()
    {
        $this->assetsHelperMock
            ->expects($this->any())
            ->method('getUrl')
            ->will($this->returnValueMap(array(
                array('/my/configured/path', null, '/my/rewritten/configured/path'),
                array('/my/explicit/path', null, '/my/rewritten/explicit/path'),
            )));

        $this->pluginManager->setPlugins(array(
            'wordcount' => array(
                'path'     => '/my/explicit/path',
                'filename' => 'plugin.js',
            ))
        );

        $form = $this->factory->create('ckeditor', null, array(
            'plugins' => array(
                'autogrow' => array(
                    'path'     => '/my/configured/path',
                    'filename' => 'plugin.js',
                ),
            ))
        );

        $view = $form->createView();

        $this->assertArrayHasKey('plugins', $view->vars);
        $this->assertSame(
            array(
                'wordcount' => array('path' => '/my/rewritten/explicit/path', 'filename' => 'plugin.js'),
                'autogrow' => array('path' => '/my/rewritten/configured/path', 'filename' => 'plugin.js'),
            ),
            $view->vars['plugins']
        );
    }

    public function testDisable()
    {
        $this->ckEditorType->isEnable(false);

        $options = array(
            'config' => array(
                'toolbar'  => array('foo' => 'bar'),
                'ui_color' => '#ffffff',
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
