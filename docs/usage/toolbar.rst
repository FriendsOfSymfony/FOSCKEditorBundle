Customize the toolbar
=====================

Built-in Toolbars
-----------------

CKEditor provides three different packages with their own configurations (full,
standard & basic). The bundle is shipped with the full edition but you can
easily switch the toolbar configuration by using the ``full``, ``standard`` or
``basic`` keyword as toolbar. You can configure it globally in your configuration:

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        configs:
            my_config:
                toolbar: full

Or you can configure it your widget:

.. code-block:: php

    $builder->add('field', 'ckeditor', array(
        'config' => array('toolbar' => 'full'),
    ));


Custom Toolbar
--------------

Build a toolbar in the configuration or especially in the widget is really a
pain. Each time, you want a custom one, you need to redefine all the structure.
To avoid this duplication, the bundle allows you to define your own toolbars or
override the built-in ones in a separate node and reuse them. This feature is
only available in your configuration.

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        configs:
            my_config_1:
                toolbar: "my_toolbar_1"
                uiColor: "#000000"
                # ...
            my_config_2:
                toolbar: "my_toolbar_2"
                uiColor: "#ffffff"
                # ...
            my_config_2:
                toolbar: "my_toolbar_1"
                uiColor: "#cccccc"
        toolbars:
            configs:
                my_toolbar_1: [ [ "Source", "-", "Save" ], "/", [ "Anchor" ], "/", [ "Maximize" ] ]
                my_toolbar_2: [ [ "Source" ], "/", [ "Anchor" ], "/", [ "Maximize" ] ]

Here, we see how is structured a toolbar. A toolbar is an array of toolbars
(strips), each one being also an array, containing a list of UI items. To do a
carriage return, you just have to add the char ``/`` between strips. It relies
on the exact same structure than CKEditor itself.

Using the toolbars node is better but the config is still not perfect as you
still have code duplications in the toolbar items. To avoid this part, you can
define a group of items in a separate node & then, inject them in your toolbar
by prefixing them with a ``@``.

.. code-block:: yaml

    fos_ck_editor:
        configs:
            my_config_1:
                toolbar: "my_toolbar_1"
                uiColor: "#000000"
                # ...
            my_config_2:
                toolbar: "my_toolbar_2"
                uiColor: "#ffffff"
                # ...
        toolbars:
            configs:
                my_toolbar_1: [ "@document", "/", "@link" , "/", "@tool" ]
                my_toolbar_2: [ "@document", "/", "@tool" ]
            items:
                document: [ "Source", "-", "Save" ]
                link:     [ "Anchor" ]
                tool:     [ "Maximize" ]

The built-in configurations (full, standard, basic) are also using items so if
you want to just override one part of a configuration, just override it:

.. code-block:: yaml

    fos_ck_editor:
        configs:
            my_config:
                toolbar: "full"
        toolbars:
            items:
                full.colors: [ "TextColor", "BGColor" ]
                full.document: [ "Source", "-", "Preview", "Print" ]

.. note::

    If you want the full list of built-in items, check the
    `FOS\CKEditorBundle\Model\ToolbarManager` class.
