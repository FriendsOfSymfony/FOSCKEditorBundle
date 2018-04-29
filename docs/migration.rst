Migration from IvoryCKEditorBundle to FOSCKEditorBundle
=======================================================

Here we will explain process of migration.

TL;DR: Check how we migrated [SonataFormatterBundle](https://github.com/sonata-project/SonataFormatterBundle/pull/331)

Update composer.json
--------------------

Replace:

.. code-block:: json

    {
     "require": {
        "egeloen/ckeditor-bundle": "*"
        }
    }

With:

.. code-block:: json

    {
     "require": {
        "friendsofsymfony/ckeditor-bundle": "^1.0"
        }
    }

Update bundle definition
------------------------

Replace::

    <?php

    // config/bundles.php
    return [
        Ivory\CKEditorBundle\IvoryCKEditorBundle::class => ['all' => true],
    ];

With::

    <?php

    // config/bundles.php
    return [
        FOS\CKEditorBundle\FOSCKEditorBundle::class => ['all' => true],
    ];

If you are not using Symfony Flex, then replace this in your AppKernel.

Replace::

    <?php

    // app/AppKernel.php

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = [
                new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
                // ...
            ];

            // ...
        }
    }

With::

    <?php

    // app/AppKernel.php

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = [
                new FOS\CKEditorBundle\FOSCKEditorBundle(),
                // ...
            ];

            // ...
        }
    }

Update configuration root key
------------------------------

Only the root key of configuration is changed.

Replace:

.. code-block:: yaml

    # config/packages/ivory_ck_editor.yaml

    ivory_ck_editor:
        configs:
            my_config:
                toolbar: [ ["Source", "-", "Save"], "/", ["Anchor"], "/", ["Maximize"] ]
                uiColor:                "#000000"
                filebrowserUploadRoute: "my_route"
                extraPlugins:           "wordcount"
                # ...

With:

.. code-block:: yaml

    # config/packages/fos_ck_editor.yaml

    fos_ck_editor:
        configs:
            my_config:
                toolbar: [ ["Source", "-", "Save"], "/", ["Anchor"], "/", ["Maximize"] ]
                uiColor:                "#000000"
                filebrowserUploadRoute: "my_route"
                extraPlugins:           "wordcount"
                # ...

If you are not using Symfony Flex, then replace root key in ``app/config/config.yml``.

Replace:

.. code-block:: yaml

    # app/config/config.yml
    ivory_ck_editor:
        configs:
            my_config:
                toolbar: [ ["Source", "-", "Save"], "/", ["Anchor"], "/", ["Maximize"] ]
                uiColor:                "#000000"
                filebrowserUploadRoute: "my_route"
                extraPlugins:           "wordcount"
                # ...
With:

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

Update namespace
----------------

The main thing that changed is the namespace, so you will have to find
all occurrences of ``Ivory\CKEditorBundle\*`` in your application and
replace it with ``FOS\CKEditorBundle\*``.

Update service definition
-------------------------

If you are fetching any of the services directly for container you will
have to find all occurrences of ``ivory_ck_editor.*`` in your application
and replace it with ``fos_ck_editor.*``.

Regenerate assets again
---------------------

You will have to regenerate your assets again, just run:

.. code-block:: bash

    bin/console assets:install
