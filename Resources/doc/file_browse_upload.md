# File Browse/Upload

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

## Integration

If you want to simplify your life, you can directly use other bundles which provides an easy integration.

### [FMElfinderBundle](https://github.com/helios-ag/FMElfinderBundle)

The FMElfinderBundle provides a [elfinder](http://elfinder.org/) integration for your Symfony2 project and at the same
time provides an integration with the IvoryCKEditorBundle.

To use it, simply follow the instruction you can find in this [documentation](https://github.com/helios-ag/FMElfinderBundle),
don't forget to enable the ckeditor behavior and configure the `filebrowserBrowseUrl` config parameter. Here, a full
example:

Install the bundle by adding the following line to your `composer.json`:

``` json
{
    "require": {
        "helios-ag/fm-elfinder-bundle": "1.*"
    }
}
```

``` bash
$ php composer.phar update helios-ag/fm-elfinder-bundle
```

Register the bundle in your `AppKernel`:

``` php
public function registerBundles()
{
    return array(
        // ...
        new FM\ElfinderBundle\FMElfinderBundle(),
    );
}
```

Warning, if you don't register the bundle in the kernel before installing it, the assets will not be populated in
your `web` directory. To fix it, simply run:

``` bash
$ php app/console assets:install --symlink
```

Register the routes in your application:

``` yaml
# app/config/routing.yml

elfinder:
     resource: "@FMElfinderBundle/Resources/config/routing.yml"
```

Configure the bundles:

``` yaml
# app/config/config.yml

ivory_ck_editor:
    default_config: default
    configs:
        default:
            filebrowserBrowseRoute: elfinder

fm_elfinder:
    editor: ckeditor
    connector:
        roots:
            uploads:
                path: uploads
```

Then, all should work as expected :)
