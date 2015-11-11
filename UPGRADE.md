# UPGRADE

### 3.0 to 4.0

The rendering of the CKEditor widget for the PHP and twig engine have been updated. Instead of taking each option as
argument, it now takes an array options allowing to more easy add new features without breaking BC. If you directly use
the `ckeditor_widget` twig function or the `$view['ckeditor']->renderWidget` php function, then you will not be
affected by the BC break. For the others, you should know use:

```
{{ ckeditor_widget(id, config, {inline: inline, input_sync: input_sync} }}
```

or

```
<?php echo $view['ckeditor']->renderWidget($id, $config, array('inline' => $inline, 'input_sync' => $input_sync)); ?>
```

### 2.5 to 3.0

All protected properties and methods have been updated to private except for entry points. This is mostly motivated for
enforcing the encapsulation and easing backward compatibility.

The `Ivory\CKEditorBundle\Templating\CKEditorHelper::renderReplace`,
`Ivory\CKEditorBundle\Twig\CKEditorExtension::renderReplace` methods and the `ckeditor_replace` twig function have
been renamed respectively to `renderWidget` and `ckeditor_widget`. There prototypes have been updated too. They
now take the newly intoduced `inline` option as third argument and so, the `inputSync` parameter have been moved
forward.

The `Ivory\CKEditorBundle\Form\Type\CKEditorType` scalar constructor values have been dropped in order to more easily
keep further backward compatibility. Then, the `ivory_ck_editor.form.type.*` related container parameters have been
dropped accordingly.

### 2.4 to 2.5

It is not possible to automatically load the ckeditor library only once. Then, the
`Ivory\CKEditorBundle\Templating\CKEditorHelper::$loaded` property, the
`Ivory\CKEditorBundle\Templating\CKEditorHelper::isLoaded` method, the
`Ivory\CKEditorBundle\Twig\CKEditorExtension::isLoaded` method and the `ckeditor_is_loaded` twig function have been
removed. The PHP and Twig templates have been updated accordingly and then, the CKEditor library is now loaded for
each fields regardless if it has already been loaded previously. Anyway, a new feature has been added in order to let
you the control of the CKEditor library loading ([doc](/Resources/doc/usage.md#load-manually-the-library)).

In order to give you the ability to control the CKEditor library loading, the
`Ivory\CKEditorBundle\Form\Type\CKEditorType` constructor has been updated. Now, it takes the autoload flag as second
argument and all others arguments have been moved forward.

### 2.3 to 2.4

The `Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper` methods have been merged into the
`Ivory\CKEditorBundle\Helper\CKEditorHelper` and so, the `Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper` class
has been removed. Additionally, the `ivory_ck_editor.helper.assets_version_trimer` service and the
`ivory_ck_editor.helper.assets_version_trimer.class` parameter has been removed.

The `Ivory\CKEditorBundle\Helper\CKEditorHelper` class has been moved to
`Ivory\CKEditorBundle\Templating\CKEditorHelper` and so, the `ivory_ck_editor.helper.templating` service has been
renamed to `ivory_ck_editor.templating.helper`. Additionally, the `Resources/config/helper.xml` file has been renamed
to `Resources/config/templating.xml`.

### 2.2 to 2.3

The CKEditor version has been upgraded from 4.3.2 to 4.4.0.

### 2.1 to 2.2

The responsibility of generating routes and assets path have been moved to a dedicated templating helper for a better
decoupling. The core assets helper and assets version trimer helper dependencies have been removed from all
managers and have been moved to this helper. Then, all constructors have been updated accordingly and all related
getter/setter have been removed. Additionally, the form type have been updated the same way and the same dependencies
have been removed. Then, its constructor and the related getter/setter have been removed.

So, the affected classes are:

 * `Ivory\CKEditorBundle\Model\ConfigManager`
 * `Ivory\CKEditorBundle\Model\PluginManager`
 * `Ivory\CKEditorBundle\Model\TemplateManager`
 * `Ivory\CKEditorBundle\Form\Type\CKEditorType`

The PHP and Twig templates have been refactored to use the new templating helper.

### 1.0 to 1.1 - 2.0 to 2.1

The `toolbar` & `uiColor` options have been removed in favor of the `config` option which allows a more flexible
configuration.

Before:

``` php
$builder->add('field', 'ckeditor', array(
    'uiColor' => '#ffffff',
    'toolbar'  => array(
        // ...
    ),
));
```

After:

``` php
$builder->add('field', 'ckeditor', array(
    'config' => array(
        'uiColor' => '#ffffff',
        'toolbar'  => array(
            // ...
        ),
        // Other options...
    ),
));
```
