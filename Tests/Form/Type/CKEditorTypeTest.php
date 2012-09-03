<?php

namespace Ivory\CKEditorBundle\Tests\Form\Type;

use Symfony\Component\Form\Tests\Extension\Core\Type\TypeTestCase;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

/**
 * CKEditor type test
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorTypeTest extends TypeTestCase
{
    /**
     * @override
     */
    protected function setUp()
    {
        parent::setUp();

        $this->factory->addType(new CKEditorType());
    }

    /**
     * Checks the default required property
     */
    public function testDefaultRequired()
    {
        $form = $this->factory->create('ckeditor');
        $view = $form->createView();
        $required = $view->get('required');

        $this->assertFalse($required);
    }

    /**
     * Checks the required property
     *
     * @expectedException \Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testRequired()
    {
        $this->factory->create('ckeditor', null, array('required' => true));
    }

    /**
     * Checks the default toolbar property
     */
    public function testDefaultToolbar()
    {
        $form = $this->factory->create('ckeditor');
        $view = $form->createView();
        $toolbar = $view->get('toolbar');

        $this->assertEquals($toolbar, array(
            array(
                'name' => 'document',
                'items' => array('Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates')
            ),
            array(
                'name' => 'clipboard',
                'items' => array('Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo')
            ),
            array(
                'name' => 'editing',
                'items' => array('Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt')
            ),
            array(
                'name' => 'forms',
                'items' => array('Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField')
            ),
            '/',
            array(
                'name' => 'basicstyles',
                'items' => array('Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat')
            ),
            array(
                'name' => 'paragraph',
                'items' => array('NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl')
            ),
            array(
                'name' => 'links',
                'items' => array('Link','Unlink','Anchor')
            ),
            array(
                'name' => 'insert',
                'items' => array('Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak')
            ),
            '/',
            array(
                'name' => 'styles',
                'items' => array('Styles','Format','Font','FontSize')
            ),
            array(
                'name' => 'colors',
                'items' => array('TextColor','BGColor')
            ),
            array(
                'name' => 'tools',
                'items' => array('Maximize', 'ShowBlocks','-','About')
            )
        ));
    }

    /**
     * Checks the toolbar property
     */
    public function testToolbar()
    {
        $form = $this->factory->create('ckeditor', null, array('toolbar' => array(
            array(
                'name' => 'name',
                'items' => array('Item1', 'Item2')
        ))));
        $view = $form->createView();
        $toolbar = $view->get('toolbar');

        $this->assertEquals($toolbar, array(
            array(
                'name' => 'name',
                'items' => array('Item1', 'Item2')
        )));
    }

    /**
     * Checks default ui color property
     */
    public function testDefaultUiColor()
    {
        $form = $this->factory->create('ckeditor');
        $view = $form->createView();
        $uiColor = $view->get('ui_color');

        $this->assertNull($uiColor);
    }

    /**
     * Checks ui color property
     */
    public function testUiColor()
    {
        $form = $this->factory->create('ckeditor', null, array('ui_color' => '#ffffff'));
        $view = $form->createView();
        $uiColor = $view->get('ui_color');

        $this->assertEquals($uiColor, '#ffffff');
    }
}
