# Usage

Before starting, you should read the Symfony2 Form documentation which is available
[here](http://symfony.com/doc/current/book/forms.html). It will give you a better understood for the next parts.

To resume, the bundle simply registers a new form field type called ``ckeditor``. This type extends the
[textarea](http://symfony.com/doc/current/reference/forms/types/textarea.html) one.

## Config

The config option is the equivalent of the
[CKEditor config option](http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html). A simple example:

``` php
$builder->add('field', 'ckeditor', array(
    'config' => array(
        'toolbar' => array(
            array(
                'name'  => 'document',
                'items' => array('Source', '-', 'Save', 'NewPage', 'DocProps', 'Preview', 'Print', '-', 'Templates'),
            ),
            '/',
            array(
                'name'  => 'basicstyles',
                'items' => array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'),
            ),
        ),
        'ui_color' => '#ffffff',
        //...
    ),
));
```

A toolbar is an array of toolbars (strips), each one being also an array, containing a list of UI items. To do a
carriage return, you just have to add the char ``/`` between strips.

## Filebrowser

To ease the CKEditor file handling, the bundle adds options which are not in CKEditor by default. These options are
related to URLs which allows to manage file browse/upload. As explain
[here](http://symfony.com/doc/current/book/routing.html), Symfony provides a powerfull routing component allowing you
to generate URLs. These concepts are directly managed by the bundle by adding three new options for each "*Url" option:

For example, the filebrowserBrowseUrl options can be generated with these three new options:

  * filebrowserBrowseRoute
  * filebrowserBrowseRouteParameters
  * filebrowserBrowseRouteAbsolute

The concerned options are:

 * filebrowserBrowseUrl
 * filebrowserFlashBrowseUrl
 * filebrowserImageBrowseUrl
 * filebrowserImageBrowseLinkUrl
 * filebrowserUploadUrl
 * filebrowserFlashUploadUrl
 * filebrowserImageUploadUrl
