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
    Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;

/**
 * CKEditor type test
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorTypeTest extends TypeTestCase
{
    /**
     * {@inheritdooc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->factory->addType(new CKEditorType());
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

        $this->assertEmpty($view->get('config'));
    }

    public function testConfig()
    {
        $options = array('config' => array('toolbar' => 'foo'));

        $form = $this->factory->create('ckeditor', null, $options);
        $view = $form->createView();

        $this->assertSame($options['config'], $view->get('config'));
    }
}
