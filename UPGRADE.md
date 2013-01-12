# UPGRADE

### 1.0.0 to 1.1.0 - 2.0.0 to 2.1.0

The `toolbar` & `ui_color` options have been removed in favor of the `config` option which allows a more flexible
configuration.

Before:

``` php
$builder->add('field', 'ckeditor', array(
    'ui_color' => '#ffffff',
    'toolbar'  => array(
        // ...
    ),
));
```

After:

``` php
$builder->add('field', 'ckeditor', array(
    'config' => array(
        'ui_color' => '#ffffff',
        'toolbar'  => array(
            // ...
        ),
        // Other options...
    ),
));
```
