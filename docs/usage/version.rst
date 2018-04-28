Use your own CKEditor
=====================

The bundle is shipped with the latest CKEditor 4 full release. If you don't want
to use it, the bundle allows you to use your own by defining it in your
configuration file or in your widget.

Install your CKEditor
---------------------

First of all, you need to download and extract your own CKEditor version in the
web directory. For that, you have two possibilities:

#. Directly put it in the web directory (``/web/ckeditor/`` for example).
#. Put it in the ``/Resources/public/`` directory of any of your bundles and
   install the assets.

Register your CKEditor
----------------------

Then, to use your own CKEditor instead of the built-in, just need to register it
in your configuration or in your widget:

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        base_path: "ckeditor"
        js_path:   "ckeditor/ckeditor.js"

.. code-block:: php

    $builder->add('field', 'ckeditor', array(
        'base_path' => 'ckeditor',
        'js_path'   => 'ckeditor/ckeditor.js',
    ));

.. note::

    Each path must be relative to the web directory.
