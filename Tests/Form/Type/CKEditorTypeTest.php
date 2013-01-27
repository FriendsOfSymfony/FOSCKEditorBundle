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

/**
 * CKEditor type test
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorTypeTest extends TypeTestCase
{
    /** @var \Ivory\CKEditorBundle\Model\ConfigManagerInterface */
    protected $configManagerMock;

    /**
     * {@inheritdooc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->configManagerMock = $this->getMock('Ivory\CKEditorBundle\Model\ConfigManagerInterface');
        $this->factory->addType(new CKEditorType($this->configManagerMock));
    }

    public function testDefaultRequired()
    {
        $form = $this->factory->create('ckeditor');
        $view = $form->createView();
        $required = $view->get('required');

        $this->assertFalse($required);
    }

    /**
     * There is a know bug in CKEditor which makes it unusable with the required HTML5 placeholder.
     *
     * @link http://dev.ckeditor.com/ticket/8031.
     *
     * @expectedException \Symfony\Component\Form\Exception\CreationException
     */
    public function testRequired()
    {
        $this->factory->create('ckeditor', null, array('required' => true));
    }

    public function testDefaultConfig()
    {
        $form = $this->factory->create('ckeditor');
        $view = $form->createView();

        $this->assertEmpty($view->get('config'));
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

        $this->assertSame($options['config'], $view->get('config'));
    }

    public function testConfigWithConfiguredConfig()
    {
        $config = array(
            'toolbar'  => 'default',
            'ui_color' => '#ffffff',
        );

        $options = array('config_name' => 'default');

        $this->configManagerMock
            ->expects($this->once())
            ->method('getConfig')
            ->with($this->equalTo('default'))
            ->will($this->returnValue($config));

        $form = $this->factory->create('ckeditor', null, $options);
        $view = $form->createView();

        $this->assertSame($config, $view->get('config'));
    }

    public function testConfigWithExplicitAndConfiguredConfig()
    {
        $configuredConfig = array(
            'toolbar'  => 'default',
            'ui_color' => '#ffffff',
        );

        $explicitConfig = array('ui_color' => '#000000');

        $options = array(
            'config_name' => 'default',
            'config'      => $explicitConfig,
        );

        $this->configManagerMock
            ->expects($this->once())
            ->method('getConfig')
            ->with($this->equalTo('default'))
            ->will($this->returnValue($configuredConfig));

        $form = $this->factory->create('ckeditor', null, $options);
        $view = $form->createView();

        $this->assertSame(array_merge($configuredConfig, $explicitConfig), $view->get('config'));
    }
}
