Skin support
============

Install your Skin
-----------------

First of all, you need to download and extract your skin in the web directory.
For that, you have two possibilities:

#. Directly put it in the web directory (``/web/ckeditor/`` for example).
#. Put it in the ``/Resources/public/`` directory of any of your bundles and
   install the assets.

Register your Skin
------------------

Then, to use your skin, just need to register it globally in your configuration:

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        default_config: my_config
        configs:
            my_config:
                skin: "skin_name,/bundles/mybundle/skins/skin_name/"

Or you can do it in your widget:

.. code-block:: php

    $builder->add('field', 'ckeditor', array(
        'config' => array('skin' => 'skin_name,/bundles/mybundle/skins/skin_name/'),
    ));

.. note::

    The skin path must be an absolute path relative to the `web` directory.
