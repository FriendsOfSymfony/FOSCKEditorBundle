# Installation

## Add IvoryCKEditorBundle to your vendor/bundles/ directory

### Using the vendors script

Add the following lines in your ``deps`` file

```
[IvoryCKEditorBundle]
    git=http://github.com/egeloen/IvoryCKEditorBundle.git
    target=/bundles/Ivory/CKEditorBundle
```

Run the vendors script

``` bash 
$ php bin/vendors update
```

### Using submodules

``` bash
$ git submodule add http://github.com/egeloen/IvoryCKEditorBundle.git vendor/bundles/Ivory/CKEditorBundle
```

## Add the Ivory namespace to your autoloader

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    'Ivory' => __DIR__.'/../vendor/bundles',
    // ...
);
```

## Add the IvoryCKEditorBundle to your application kernel

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    return array(
        new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
        // ...
    );
}
```

## Populate the assets

Run the symfony command

``` bash
$ php app/console assets:install web
```

Next : [Usage](http://github.com/egeloen/IvoryCKEditorBundle/blob/master/Resources/doc/usage.md)
