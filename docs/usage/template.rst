Template support
================

Enable the Templates Plugin
---------------------------

The bundle offers you the ability to manage extra templates. To use this
feature, you need to enable the ``templates`` plugins shipped with the bundle.
You can define it globally in your configuration:

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        default_config: my_config
        configs:
            my_config:
                extraPlugins: "templates"

Or you can define it in your widget:

.. code-block:: php

    $builder->add('field', 'ckeditor', array(
        'config' => array(
            'extraPlugins' => 'templates',
        ),
    ));

Configure your templates
------------------------

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
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

Use a dedicated template
------------------------

If you prefer define your html in a dedicated Twig or PHP template, you can
replace the ``html`` node by the ``template`` one and provide the path of your
template. You can optionally provide template parameters with the
``template_parameters`` node.

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
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
                        template:    "AppBundle:CKEditor:template.html.twig"
                        template_parameters:
                            foo: bar

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
                        'title'               => 'My Template',
                        'image'               => 'images.jpg',
                        'description'         => 'My awesome template',
                        'template'            => 'AppBundle:CKEditor:template.html.twig',
                        'template_parameters' => array('foo' => 'bar'),
                    ),
                    // ...
                ),
            ),
        ),
    ));
