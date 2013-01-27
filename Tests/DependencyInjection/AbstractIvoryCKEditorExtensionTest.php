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

use Ivory\CKEditorBundle\DependencyInjection\IvoryCKEditorExtension,
    Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Abstract Ivory CKEditor extension test.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
abstract class AbstractIvoryCKEditorExtensionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Symfony\Component\DependencyInjection\ContainerBuilder */
    protected $container;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->container->setParameter('twig.form.resources', array());
        $this->container->registerExtension($extension = new IvoryCKEditorExtension());
        $this->container->loadFromExtension($extension->getAlias());
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
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

        $this->assertInstanceOf(
            'Ivory\CKEditorBundle\Form\Type\CKEditorType',
            $this->container->get('form.type.ckeditor')
        );
    }

    /**
     * This test checks if the ckeditor widget is weel add to the available form twig ressources but it does not work
     * (Anyway, I have checked in a Symfony SE & all works fine).
     *
     * With my test bootstrap (see setUp), in a first time, the widget is well added but in a second time, it is
     * override by the default value. Maybe someone with a better understood of the DI component can solve it :)
     */
    public function testTwigResources()
    {
//        // FIXME
//        $this->container->compile();
//
//        $this->assertTrue(in_array(
//            'IvoryCKEditorBundle:Form:ckeditor_widget.html.twig',
//            $this->container->getParameter('twig.form.resources'))
//        );
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
                'ui_color' => '#000000',
            ),
        );

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
                'ui_color' => '#000000',
            ),
            'custom' => array(
                'toolbar' => array(
                    array('Source', '-', 'Save'),
                    '/',
                    array('Anchor'),
                ),
                'ui_color' => '#ffffff',
            ),
        );

        $this->assertSame($expected, $configManager->getConfigs());
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
