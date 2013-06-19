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
        'uiColor' => '#ffffff',
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

## Plugins support

The bundle offers you the ability to manage extra plugins. To understand how it works, you will enable the
[wordcount](http://ckeditor.com/addon/wordcount) plugin for our CKEditor widget.

### Install

First, you need to download & extract it in the web directory. For that, you have 2 possibilities:

  - Directly put the plugin in the web directory (`/web/ckeditor/plugins/` for example).
  - Put the plugin in the `/Resources/public/` directory of any of your bundles.

### Register

In order to load it, you need to specify his location to the bundle. For that, you can pass it as option to the widget:

``` php
$builder->add('field', 'ckeditor', array(
    'plugins' => array(
        'wordcount' => array(
            'path'     => '/bundles/mybundle/wordcount/',
            'filename' => 'plugin.js',
        ),
    ),
));
```

The plugin can now be used but if you do that, the plugin will only be usable for this form. If you prefer enable
plugins for all CKEditor widget, you should register them in your configuration file:

```
ivory_ck_editor:
    plugins:
        wordcount:
            path:     "/bundles/mybundle/wordcount/"
            filename: "plugin.js"
```

### Use it

To use it, simply add it as `extraPlugins` in the ckeditor widget config:

``` php
$builder->add('field', 'ckeditor', array(
    'config' => array(
        'extraPlugins' => 'wordcount',
    ),
));
```

## Fallback to textarea for testing purpose

Sometime you don't want to use the CKEditor widget but a simple textarea (e.g testing purpose). As CKEditor uses an
iframe to render the widget, it can be difficult to automate something on it. To disable CKEditor and fallback on the
parent widget (textarea), simply disable it in your configuration file or in your widget:

```
# app/config/config_test.yml
ivory_ck_editor:
    enable: false
```

``` php
$builder->add('field', 'ckeditor', array('enable' => false));
```

## Use your own CKEditor version

The bundle is shipped with the latest CKEditor 4 full release. If you don't want to use it, the bundle allows you to
use your own by defining it in your configuration file or in your widget.

First of all, you need to download & extract your version in the web directory. For that, you have 2 possibilities:

  - Directly put it in the web directory (`/web/ckeditor/` for example).
  - Put it in the `/Resources/public/` directory of any of your bundles.

Then, register it:

```
# app/config/config.yml
ivory_ck_editor:
    base_path: "ckeditor"
    js_path:   "ckeditor/ckeditor.js"
```

``` php
$builder->add('field', 'ckeditor', array(
    'base_path' => 'ckeditor',
    'js_path'   => 'ckeditor/ckeditor.js',
));
```

**Each path is relative to the web directory**
