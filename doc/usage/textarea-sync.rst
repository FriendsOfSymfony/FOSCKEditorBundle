Synchronize the textarea
========================

When the textarea is transformed into a CKEditor widget, the textarea value is
no more populated except when the form is submitted. Then, it leads to issues
when you try to serialize the form or you try to rely on the textarea value in
JavaScript. To automatically synchronize the textarea value, you can do it
globally in your configuration:

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        input_sync: true

Or you can do it in your widget:

.. code-block:: php

    $builder->add('field', 'ckeditor', array('input_sync' => true));
