Template support
================

The bundle offers you the ability to manage extra templates. To use this
feature, you need to enable the ``templates`` plugins shipped with the bundle
and configure your templates. Like plugins, you can define them globally in
your configuration:

.. code-block:: yaml

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

Or you can define them in your widget:

.. code-block:: php

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
