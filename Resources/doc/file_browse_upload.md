# File Browse/Upload

To ease the CKEditor file handling, the bundle adds options which are not in CKEditor by default. These options are
related to URLs which allows to manage file browse/upload. As explain
[here](http://symfony.com/doc/current/book/routing.html), Symfony provides a powerfull routing component allowing you
to generate URLs. These concepts are directly managed by the bundle by adding three new options for each "*Url" option:

For example, the filebrowserBrowseUrl option can be generated with these three new options:

  * filebrowserBrowseRoute
  * filebrowserBrowseRouteParameters
  * filebrowserBrowseRouteAbsolute

``` php
$builder->add('field', 'ckeditor', array(
    'config' => array(
        'filebrowserBrowseRoute'           => 'my_route',
        'filebrowserBrowseRouteParameters' => array('slug' => 'my-slug'),
        'filebrowserBrowseRouteAbsolute'   => true,
    ),
));
```

If this process does not fit your needs, you can use the `filebrowser*Handler` option allowing you to build your own
url with a simple closure:

``` php
$builder->add('field', 'ckeditor', array(
    'config' => array(
        'filebrowserBrowseHandler' => function (RouterInterface $router) {
            return $router->generate('my_route', array('slug' => 'my-slug', true);
        },
    ),
));
```

A closure will allow you to use the `use` keyword in order to make it aware of your own dependencies :)

These features are about the following options:

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

To use it, just read this [documentation](https://github.com/helios-ag/FMElfinderBundle#using-elfinder-with-ckeditorbundle).

### [CoopTilleulsCKEditorSonataMediaBundle](https://github.com/coopTilleuls/CoopTilleulsCKEditorSonataMediaBundle/)

The CoopTilleulsCKEditorSonataMediaBundle provides [SonataMedia](http://sonata-project.org/bundles/media) integration with CKEditor and IvoryCKEditorBundle.

Read [installation instructions](https://github.com/coopTilleuls/CoopTilleulsCKEditorSonataMediaBundle/blob/master/Resources/doc/install.md).

The working example:

Install and configure [SonataMediaBundle](http://sonata-project.org/bundles/media/master/doc/index.html).

Add CoopTilleulsCKEditorSonataMediaBundle in your `composer.json` file:

``` json
{
    "require": {
        "tilleuls/ckeditor-sonata-media-bundle": "~1.0",
    }
}
```

Register the bundle in your AppKernel:

``` php
// app/AppKernel.php

public function registerBundles()
{
    return array(
        // ...
        new CoopTilleuls\Bundle\CKEditorSonataMediaBundle\CoopTilleulsCKEditorSonataMediaBundle(),
        // ...
    );
}
```

Install bundles:

```
$ composer update
```

Configure IvoryCKEditorBundle to use the bundle as file browser:

``` yaml
# app/config/config.yml

ivory_ck_editor:
    default_config: default
    configs:
        default:
            filebrowserBrowseRoute: admin_sonata_media_media_browser
            filebrowserImageBrowseRoute: admin_sonata_media_media_browser
            # Display images by default when clicking the image dialog browse button
            filebrowserImageBrowseRouteParameters:
                provider: sonata.media.provider.image
            filebrowserUploadRoute: admin_sonata_media_media_upload
            filebrowserUploadRouteParameters:
                provider: sonata.media.provider.file
            # Upload file as image when sending a file from the image dialog
            filebrowserImageUploadRoute: admin_sonata_media_media_upload
            filebrowserImageUploadRouteParameters:
                provider: sonata.media.provider.image
                context: my-context # Optional, to upload in a custom context
```
