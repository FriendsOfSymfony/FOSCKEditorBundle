How to handle file browse/upload
================================

Before starting, be aware there is nothing which will automatically handle file
browse/upload for you in this bundle (it's out of scope). So, you will need to
implement it by yourself and then configure your browse/upload URIs or routes in
the CKEditor configuration or in the widget.

Supported Options
-----------------

CKEditor natively supports different options according to what you want to
browse or upload. This options should be URIs which point to your controllers.
The available options are:

* filebrowserBrowseUrl
* filebrowserFlashBrowseUrl
* filebrowserImageBrowseUrl
* filebrowserImageBrowseLinkUrl
* filebrowserUploadUrl
* filebrowserFlashUploadUrl
* filebrowserImageUploadUrl

Routing Options
---------------

To ease the CKEditor file handling, the bundle adds options which are not in
CKEditor by default. These options are related to the Symfony `Routing Component`_
and allow you to configure routes instead of URIs. For each ``*Url`` option,
three new options are available.

For example, the ``filebrowserBrowseUrl`` option can be generated with these
three new options:

* filebrowserBrowseRoute
* filebrowserBrowseRouteParameters
* filebrowserBrowseRouteAbsolute

Static Routing
~~~~~~~~~~~~~~

If your routing is static, you can configure these options globally in your
configuration:

.. code-block:: yaml

    # app/config/config.yml
    ivory_ck_editor:
        default_config: my_config
        config:
            my_config:
                filebrowserBrowseRoute:           "my_route"
                filebrowserBrowseRouteParameters: { slug: "my-slug" }
                filebrowserBrowseRouteAbsolute:   true

Or you can configure it your widget:

.. code-block:: php

    $builder->add('field', 'ckeditor', array(
        'config' => array(
            'filebrowserBrowseRoute'           => 'my_route',
            'filebrowserBrowseRouteParameters' => array('slug' => 'my-slug'),
            'filebrowserBrowseRouteAbsolute'   => true,
        ),
    ));

Dynamic Routing
~~~~~~~~~~~~~~~

If the static routing does not fit your needs, you can use the
``filebrowser*Handler`` option allowing you to build your own url with a simple
but much more powerful closure and so make it aware of your dependencies:

.. code-block:: php

    // A blog post...
    $post = $manager->find($id);

    $builder->add('field', 'ckeditor', array(
        'config' => array(
            'filebrowserBrowseHandler' => function (RouterInterface $router) use ($post) {
                return $router->generate('my_route', array('slug' => $post->getSlug(), true);
            },
        ),
    ));

Integration
-----------

If you want to simplify your life, you can directly use other bundles which have
already integrated the concept explain in the previous chapter.

CoopTilleulsCKEditorSonataMediaBundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The `CoopTilleulsCKEditorSonataMediaBundle`_ provides a `SonataMedia`_
integration with this bundle.

FMElfinderBundle
~~~~~~~~~~~~~~~~

The `FMElfinderBundle`_ provides a `ELFinder`_ integration with this bundle.

.. _`Routing Component`: http://symfony.com/doc/current/book/routing.html
.. _`CoopTilleulsCKEditorSonataMediaBundle`: https://github.com/coopTilleuls/CoopTilleulsCKEditorSonataMediaBundle
.. _`SonataMedia`: http://sonata-project.org/bundles/media
.. _`FMElfinderBundle`: https://github.com/helios-ag/FMElfinderBundle
.. _`ELFinder`: http://elfinder.org
