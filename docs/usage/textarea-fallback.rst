Fallback to textarea
====================

Sometimes, you don't want to use the CKEditor widget but a simple textarea (e.g
testing purpose). As CKEditor uses an iFrame to render the widget, it can be
difficult to automate something on it. To disable CKEditor and fallback on the
parent widget (textarea), you can disable it globally in your configuration:

.. code-block:: yaml

    # app/config/config_test.yml
    fos_ck_editor:
        enable: false

Or you can disable it in your widget:

.. code-block:: php

    $builder->add('field', 'ckeditor', array('enable' => false));
