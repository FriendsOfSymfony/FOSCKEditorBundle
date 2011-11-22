# Usage

Before starting, I recommend you to read the Symfony2 Form documentation which is available [here](http://symfony.com/doc/current/book/forms.html).

The IvoryCKEditorBundle adds the form field type ``ckeditor`` to the Form Component.

## Available options

### Toolbar

   - option: toolbar
   - type: array

It is an array of toolbars (strips), each one being also an array, containing a list of UI items.
To do a carriage return, you just have to add the char ``/`` between strips.

#### Default toolbar

``` php
<?php

$toolbar = array(
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
);
```

### UI Color

   - option: ui_color
   - type: string
   - default: none

Describes the base user interface color to be used by the editor.

### Max length

   - option: max_length
   - type: integer

This option is used to add a max_length attribute, which is used by some browsers to limit the amount of text in a field.

### Label

   - option: label
   - type: string 
   - default: The label is "guessed" from the field name

Sets the label that will be used when rendering the field. 

The label can also be directly set inside the template:

```
{{ render_label(form.name, 'Your name') }}
```

### Trim

   - option: trim
   - type: Boolean
   - default: true

If true, the whitespace of the submitted string value will be stripped via the trim() function when the data is bound. 
This guarantees that if a value is submitted with extra whitespace, it will be removed before the value is merged back onto the underlying object.

### Read Only

   - option: read_only
   - type: Boolean
   - default: false

If this option is true, the field will be rendered with the disabled attribute so that the field is not editable.

### Error Bubbling

   - option: error_bubbling¶
   - type: Boolean
   - default: false

If true, any errors for this field will be passed to the parent field or form. 
For example, if set to true on a normal field, any errors for that field will be attached to the main form, not to the specific field.

Prevous: [Installation](http://github.com/egeloen/IvoryCKEditorBundle/blob/master/Resources/doc/installation.md)
Next: [Test](http://github.com/egeloen/IvoryCKEditorBundle/blob/master/Resources/doc/test.md)