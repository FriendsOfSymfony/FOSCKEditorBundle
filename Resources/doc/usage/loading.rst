Load manually CKEditor
======================

By default, all fields loads the CKEditor library. It means if you have multiple
CKEditor fields, there will be multiple CKEditor library loading (as much as you
have fields). If you want to control it, you can configure the bundle to not
load the library at all and let you the control of it. To disable the CKEditor
library loading, you can do it globally in your configuration:

.. code-block:: yaml

    # app/config/config.yml
    ivory_ck_editor:
        autoload: false

Or you can disable it in your widget:

.. code-block:: php

    $builder->add('field', 'ckeditor', array('autoload' => false));

.. note::

    CKEditor must be loaded before any fields have been rendered, so we
    recommend you to register it in the ``head`` of your page.
