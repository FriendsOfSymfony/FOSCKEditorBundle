<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder,
    Symfony\Component\Form\FormView,
    Symfony\Component\Form\FormInterface;

/**
 * CKEditor type
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->setAttribute('toolbar', $options['toolbar'])
            ->setAttribute('ui_color', $options['ui_color']);
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $view
            ->set('toolbar', $form->getAttribute('toolbar'))
            ->set('ui_color', $form->getAttribute('ui_color'));
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'required' => false,
            'toolbar' => array(
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
            ),
            'ui_color' => null
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAllowedOptionValues(array $options)
    {
        return array('required' => array(false));
    }
    
    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ckeditor';
    }
}
