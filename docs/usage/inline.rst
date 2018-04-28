Use inline editing
==================

By default, the bundle uses a `Classic Editing`_ which relies on
``CKEDITOR.replace``. If you want to use the `Inline Editing`_ which relies on
``CKEDITOR.inline``, you can configure it globally in your configuration:

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        inline: true

Or you can configure it in your widget:

.. code-block:: php

    $builder->add('field', 'ckeditor', array('inline' => true));

.. _`Classic Editing`: http://docs.ckeditor.com/#!/guide/dev_framed
.. _`Inline Editing`: http://docs.ckeditor.com/#!/guide/dev_inline
