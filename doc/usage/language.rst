Manage language
===============

Automatic language
------------------

By default, the bundle will try to automatically guess the language of your editor
according to the request locale. If it is not available, it will fallback on the
``locale`` container parameter. If it is also not available, the editor language
cannot be guessed and so, the editor will use the default editor language.

Explicit language
-----------------

CKEditor allows you to customize the language used by the editor via the
``language`` config option. If you define this option, this explicit language
will be used instead of the automatic one. You can do it globally in your
configuration:

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        configs:
            my_config:
                language: fr

Or you can do it in your widget:

.. code-block:: php

    $builder->add('field', 'ckeditor', 'config' => array(
        'language' => 'fr',
    ));
