# Usage

Before starting, you should read the Symfony2 Form documentation which is available
[here](http://symfony.com/doc/current/book/forms.html). It will give you a better understanding for the next parts.

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

**NOTE** As of Symfony 2.8, the second parameter for ``$builder->add()`` accepts the fully qualified class name of the
form type and is required in Symfony 3.0. In these Symfony versions, you should replace the ``ckeditor`` parameter value
with ``Ivory\CKEditorBundle\Form\Type\CKEditorType`` or its constant ``CKEditorType::class`` if your on PHP 5.5 or 
upper.

A toolbar is an array of toolbars (strips), each one being also an array, containing a list of UI items. To do a
carriage return, you just have to add the char ``/`` between strips.

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

## Skin support

Download a skin from [CKEditor](http://ckeditor.com/addons/skins/all) & extract it in the web or bundle resource directory:

ex: `/web/ckeditor/skins/`

### Register

Place the following code on your config.yml and change the skin name

```
ivory_ck_editor:
    configs:
        my_config:
            skin: "skin_name,/absolute/web/skin/path/" # ex: ckeditor/skins/skin_name/ if you placed in the web directory
```

## StylesSet support

The bundle allows you to define your own styles. Like plugins, you can define them at the form level or in your
configuration file:

``` php
$builder->add('field', 'ckeditor', array(
    'config' => array(
        'stylesSet' => 'my_styles',
    ),
    'styles' => array(
        'my_styles' => array(
            array('name' => 'Blue Title', 'element' => 'h2', 'styles' => array('color' => 'Blue')),
            array('name' => 'CSS Style', 'element' => 'span', 'attributes' => array('class' => 'my_style')),
            array('name' => 'Multiple Element Style', 'element' => array('h2', 'span'), 'attributes' => array('class' => 'my_class')),
            array('name' => 'Widget Style', 'type' => 'widget' => 'widget' => 'my_widget', 'attributes' => array('class' => 'my_widget_style')),
        ),
    ),
));
```

``` yaml
# app/config/config.yml
ivory_ck_editor:
    default_config: my_config
    configs:
        my_config:
            stylesSet: "my_styles"
    styles:
        my_styles:
            - { name: "Blue Title", element: "h2", styles: { color: "Blue" }}
            - { name: "CSS Style", element: "span", attributes: { class: "my_style" }}
            - { name: "Widget Style", type: widget, widget: "my_widget", attributes: { class: "my_widget_style" }}
```

## Templates support

The bundle offers you the ability to manage extra templates. To use this feature, you need to enable the `templates`
plugins shipped with the bundle and configure your templates. Like plugins, you can define them at the form level or in
your configuration file:

``` php
$builder->add('field', 'ckeditor', array(
    'config' => array(
        'extraPlugins' => 'templates',
        'templates'    => 'my_template',
    ),
    'templates' => array(
        'my_template' => array(
            'imagesPath' => '/bundles/mybundle/templates/images',
            'templates'  => array(
                array(
                    'title'       => 'My Template',
                    'image'       => 'images.jpg',
                    'description' => 'My awesome template',
                    'html'        => '<p>Crazy template :)</p>',
                ),
                // ...
            ),
        ),
    ),
));
```

``` yaml
# app/config/config.yml
ivory_ck_editor:
    default_config: my_config
    configs:
        my_config:
            extraPlugins: "templates"
            templates:    "my_templates"
    templates:
        my_templates:
            imagesPath: "/bundles/mybundle/templates/images"
            templates:
                -
                    title:       "My Template"
                    image:       "image.jpg"
                    description: "My awesome template"
                    html:        "<p>Crazy template :)</p>"
```

## Load manually the library

By default, all fields loads the CKEditor library. It means if you have multiple CKEditor fields, there will be
multiple CKEditor library loading (as much as you have fields). If you want to control it, you can configure the
bundle to not load the library at all and let you the control of it. To disable the CKEditor libary loading, you can
do it globally:

``` yaml
ivory_ck_editor:
    autoload: false
```

Or, if you just want to disable it for a specific field, you can use:

``` php
$builder->add('field', 'ckeditor', array('autoload' => false));
```

Be aware, the library must be loaded before any field have been rendered.

## Inline support

By default, the bundle uses a [classic editing](http://docs.ckeditor.com/#!/guide/dev_framed) which relies on
`CKEDITOR.replace`. If you want to use the [inline editing](http://docs.ckeditor.com/#!/guide/dev_inline) which relies
on `CKEDITOR.inline`, you can configure it globally:

``` yaml
ivory_ck_editor:
    inline: true
```

Or, if you just want to enable it for a specific field, you can use:

``` php
$builder->add('field', 'ckeditor', array('inline' => true));
```

## Disable auto inline

By default, CKEditor enables the auto inline feature meaning that any `contenteditable` attribute sets to `true` will
be converted to ckeditor instance automatically. If you want to disable it, you can do it globally:

``` yaml
ivory_ck_editor:
    auto_inline: false
```

Or, if you just want to disable it at runtime, you can use:

``` php
$builder->add('field', 'ckeditor', array('auto_inline' => false));
```

Be aware this option will only disable the CKEditor auto inline feature not the browser one if it supports it.

## JQuery adapter

The CKEditor JQuery adapter is by default not loaded even if the `autoload` option is enabled. In order to load it,
the `autoload` flag must be enabled and you must explicitly enable the jquery adapter. You can do it globally:

``` yaml
ivory_ck_editor:
    jquery: true
```

Or, if you just want to enable it for a specific field, you can use:

``` php
$builder->add('field', 'ckeditor', array('jquery' => true));
```

We recommend to use JQuery adapter if your app relies on JQuery.
It allows to wrap CKEditor code in `$(document).ready()`

Additionally, by default, the JQuery adapter used is the [one](/Resources/public/adapters/jquery.js) shipped with the
bundle. If you want to use your own, you can configure it globally:

``` yaml
ivory_ck_editor:
    jquery_path: your/own/jquery.js
```

Or, you can configure it just for a specific field:

``` php
$builder->add('field', 'ckeditor', array('jquery_path' => 'your/own/jquery.js'));
```

## Synchronize the textarea

When the textarea is transformed into a CKEditor widget, the textarea value is no more populated except for form
submission. Then, it leads to issues when you try to serialize form in javascript or you try to rely on the textare
value. To automatically synchronize the textarea value, you can do it globally:

``` yaml
ivory_ck_editor:
    input_sync: true
```

Or, you can do it locally:

``` php
$builder->add('field', 'ckeditor', array('input_sync' => true));
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
