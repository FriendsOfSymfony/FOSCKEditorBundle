# UPGRADE

### 1.1.0 to 1.2.0 - 2.1.0 to 2.2.0

The `ConfigManagerInterface` now supports default configuration. You need to implements two new methods:

 * getDefaultConfig
 * setDefaultConfig

### 1.0.0 to 1.1.0 - 2.0.0 to 2.1.0

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
