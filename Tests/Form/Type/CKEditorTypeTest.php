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

    public function testDefaultToolbar()
    {
        $form = $this->factory->create('ckeditor');
        $view = $form->createView();

        $toolbar = array(
            array(
                'name'  => 'document',
                'items' => array('Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates')
            ),
            array(
                'name'  => 'clipboard',
                'items' => array('Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo')
            ),
            array(
                'name'  => 'editing',
                'items' => array('Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt')
            ),
            array(
                'name'  => 'forms',
                'items' => array('Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField')
            ),
            '/',
            array(
                'name'  => 'basicstyles',
                'items' => array('Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat')
            ),
            array(
                'name'  => 'paragraph',
                'items' => array('NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl')
            ),
            array(
                'name'  => 'links',
                'items' => array('Link','Unlink','Anchor')
            ),
            array(
                'name'  => 'insert',
                'items' => array('Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak')
            ),
            '/',
            array(
                'name'  => 'styles',
                'items' => array('Styles','Format','Font','FontSize')
            ),
            array(
                'name'  => 'colors',
                'items' => array('TextColor','BGColor')
            ),
            array(
                'name'  => 'tools',
                'items' => array('Maximize', 'ShowBlocks','-','About')
            )
        );

        $this->assertSame($toolbar, $view->get('toolbar'));
    }

    public function testToolbar()
    {
        $toolbar = array(
            array(
                'name'  => 'name',
                'items' => array('Item1', 'Item2'),
            ),
        );

        $form = $this->factory->create('ckeditor', null, array('toolbar' => $toolbar));
        $view = $form->createView();

        $this->assertSame($toolbar, $view->get('toolbar'));
    }

    public function testDefaultUiColor()
    {
        $form = $this->factory->create('ckeditor');
        $view = $form->createView();

        $this->assertNull($view->get('ui_color'));
    }

    public function testUiColor()
    {
        $uiColor = '#ffffff';

        $form = $this->factory->create('ckeditor', null, array('ui_color' => $uiColor));
        $view = $form->createView();

        $this->assertSame($uiColor, $view->get('ui_color'));
    }
}
