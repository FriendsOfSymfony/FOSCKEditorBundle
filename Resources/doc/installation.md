# Installation

## Symfony 2.1.*

Require the bundle in your composer.json file:

```
{
    "require": {
        "egeloen/ckeditor-bundle": "2.*",
    }
}
```

Register the bundle:

``` php
// app/AppKernel.php

public function registerBundles()
{
    return array(
        new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
        // ...
    );
}
```

Install the bundle:

```
$ composer update
```

## Symfony 2.0.*

Add Ivory CKEditor bundle to your deps file:

```
[IvoryCKEditorBundle]
    git=http://github.com/egeloen/IvoryCKEditorBundle.git
    target=bundles/Ivory/CKEditorBundle
    version=1.0.0
```

Autoload the Ivory CKEditor bundle namespaces:

``` php
// app/autoload.php

$loader->registerNamespaces(array(
    'Ivory\\CKEditorBundle' => __DIR__.'/../vendor/bundles',
    // ...
);
```

Register the bundle:

``` php
// app/AppKernel.php

public function registerBundles()
{
    return array(
        new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
        // ...
    );
}
```

Run the vendors script:

``` bash
$ php bin/vendors install
```
