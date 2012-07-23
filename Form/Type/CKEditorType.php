<?php

namespace Ivory\CKEditorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAttribute('toolbar', $options['toolbar'])
            ->setAttribute('ui_color', $options['ui_color']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'toolbar'      => $form->getAttribute('toolbar'),
            'ui_color'     => $form->getAttribute('ui_color'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
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
        ));

        $resolver->addAllowedValues(array('required' => array(false)));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
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
