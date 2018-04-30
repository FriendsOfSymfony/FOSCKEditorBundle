Define reusable configuration
=============================

The CKEditor bundle provides an advanced configuration which can be reused on
multiple CKEditor instances. Instead of duplicate the configuration on each form
builder, you can directly configure it once and reuse it all the time. The
bundle allows you to define as many configurations as you want.

.. tip::

    Check out the full list of `CKEditor configuration options`_.

Define a configuration
----------------------

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        configs:
            my_config:
                toolbar: [ ["Source", "-", "Save"], "/", ["Anchor"], "/", ["Maximize"] ]
                uiColor:                "#000000"
                filebrowserUploadRoute: "my_route"
                extraPlugins:           "wordcount"
                # ...

.. tip::

    The config node is a variable node meaning you can put any CKEditor
    configuration options in it.

.. note::

    The first configuration defined will be used as default configuration
    if you don't explicitly configure it.

Use a configuration
-------------------

When you have defined a config, you can use it with the ``config_name`` option:

.. code-block:: php

    $builder->add('field', 'ckeditor', array(
        'config_name' => 'my_config',
    ));

Override a configuration
------------------------

If you want to override some parts of the defined config, you can still use the
``config`` option:

.. code-block:: php

    $builder->add('field', 'ckeditor', array(
        'config_name' => 'my_config',
        'config'      => array('uiColor' => '#ffffff'),
    ));

Define default configuration
----------------------------

If you want to define your configuration globally to use it by default without
having to use the ``config_name`` option, you can use the ``default_config``
node:

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        default_config: my_config
        configs:
            my_config:
                # ...

.. _`CKEditor configuration options`: http://docs.ckeditor.com/#!/api/CKEDITOR.config
