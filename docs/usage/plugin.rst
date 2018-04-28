Plugin support
==============

The bundle offers you the ability to manage extra plugins. To understand how it
works, you will enable the `Wordcount`_ plugin for our CKEditor widget.

Install the Plugin
------------------

First, you need to download and extract it in the web directory. For that, you
have two possibilities:

#. Directly put the plugin in the web directory (``/web/ckeditor/plugins/`` for
   example).
#. Put the plugin in the ``/Resources/public/`` directory of any of your bundles.

Register the Plugin
-------------------

In order to load it, you need to specify its location. For that, you can do it
globally in your configuration:

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        default_config: my_config
        configs:
            my_config:
                extraPlugins: "wordcount"
        plugins:
            wordcount:
                path:     "/bundles/mybundle/wordcount/"
                filename: "plugin.js"

Or you can do it in your widget:

.. code-block:: php

    $builder->add('field', 'ckeditor', array(
        'config' => array(
            'extraPlugins' => 'wordcount',
        ),
        'plugins' => array(
            'wordcount' => array(
                'path'     => '/bundles/mybundle/wordcount/',
                'filename' => 'plugin.js',
            ),
        ),
    ));

Plugin dependency
-----------------

Once your plugin is installed and registered, you will also need to install and
register these dependencies. Hopefully, the ``wordcount`` has no extra dependency
but other plugin can require extra ones. So if it is the case, you need to redo
the process for them and so on.

Plugin icon
-----------

If you don't configure a built-in toolbar or a custom toolbar, the plugin icon
should be visible automatically according to the plugin configuration otherwise,
it is your responsibility to configure it. Take a look to this
:doc:`documentation <toolbar>`.

.. _`Wordcount`: http://ckeditor.com/addon/wordcount
