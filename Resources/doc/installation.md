# Installation

Require the bundle in your composer.json file:

```
$ composer require egeloen/ckeditor-bundle --no-update
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
$ composer update egeloen/ckeditor-bundle
```
