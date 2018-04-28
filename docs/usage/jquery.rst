jQuery adapter
==============

If your application relies on jQuery, we recommend you to use the jQuery
adapter. The bundle will automatically wrap the CKEditor instantiation into a
``jQuery(document).ready()`` block making the code more reliable.

Enable the Adapter
------------------

The CKEditor jQuery adapter is by default not loaded even if the ``autoload``
option is enabled. In order to load it, the ``autoload`` flag must be enabled
and you must explicitly enable the jQuery adapter. You can do it globally in
your configuration:

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        jquery: true

Or you can do it in your widget:

.. code-block:: php

    $builder->add('field', 'ckeditor', array('jquery' => true));

Use your Adapter
----------------

Additionally, the jQuery adapter used by default is the one shipped with the
bundle in ``Resources/public/adapters/jquery.js``. If you would prefer use
your own, you can configure it globally in your configuration:

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        jquery_path: your/own/jquery.js

Or you can configure it in your widget:

.. code-block:: php

    $builder->add('field', 'ckeditor', array('jquery_path' => 'your/own/jquery.js'));

.. note::

    Each path must be relative to the web directory.
