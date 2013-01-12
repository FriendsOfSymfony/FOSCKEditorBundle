# Usage

Before starting, I recommend you to read the Symfony2 Form documentation which is available [here](http://symfony.com/doc/current/book/forms.html).

The IvoryCKEditorBundle adds the form field type ``ckeditor`` to the Form Component.

## Available options

### Config

The config option is an equivalent of the [CKEditor config option](http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html).
Then, if you want to customize your toolbar or your ui color for example, you can do:

``` php
$builder->add('field', 'ckeditor', array(
    'config' => array(
        'toolbar' => array(
            array(
                'name'  => 'document',
                'items' => array('Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates'),
            ),
            '/',
            array(
                'name'  => 'basicstyles',
                'items' => array('Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat'),
            ),
        ),
        'ui_color' => '#ffffff',
        //...
    ),
));
```

A toolbar is an array of toolbars (strips), each one being also an array, containing a list of UI items. To do a
carriage return, you just have to add the char ``/`` between strips.

### Max length

   - option: max_length
   - type: integer
   - default: none

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
This guarantees that if a value is submitted with extra whitespace, it will be removed before the value is merged back
onto the underlying object.

### Read Only

   - option: read_only
   - type: Boolean
   - default: false

If this option is true, the field will be rendered with the disabled attribute so that the field is not editable.

### Error Bubbling

   - option: error_bubbling
   - type: Boolean
   - default: false

If true, any errors for this field will be passed to the parent field or form.
For example, if set to true on a normal field, any errors for that field will be attached to the main form, not to the
specific field.

Previous: [Installation](http://github.com/egeloen/IvoryCKEditorBundle/blob/master/Resources/doc/installation.md)
