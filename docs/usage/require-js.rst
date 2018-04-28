RequireJS Support
=================

If your application relies on RequireJS, we recommend you to enable its
support. The bundle will automatically wrap the CKEditor instantiation into
a ``require`` block making the code more reliable.

Configure RequireJS
-------------------

The first step is to configure RequireJS in order to make it aware of where
CKEditor is located. For that, you can use the following snippet:

.. code-block:: js

    {
        paths: {
            'ckeditor': '{{ asset("bundles/fosckeditor/ckeditor") }}'
        },
        shim: {
            'ckeditor': {
                deps: ['jQuery'],
                exports: 'CKEDITOR'
            }
        }
    }

Enable RequireJS
----------------

The second step is to enable RequireJS in the bundle. To do so, you can
configure it globally in you configuration:

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        require_js: true

Or you can configure it in your widget:

.. code-block:: php

    $builder->add('field', 'ckeditor', array('require_js' => true));
