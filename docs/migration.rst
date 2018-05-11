Migration from IvoryCKEditorBundle to FOSCKEditorBundle
=======================================================

Here we will explain the process of migration.

TL;DR: Check how we migrated `SonataFormatterBundle`_

Update composer.json
--------------------

.. code-block:: bash

    composer remove egeloen/ckeditor-bundle
    composer require friendsofsymfony/ckeditor-bundle

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

Only the root key of the configuration is changed.

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

If you are not using Symfony Flex, then replace the root key in ``app/config/config.yml``.

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
replace them with ``FOS\CKEditorBundle\*``.

Before::

    <?php

    use Ivory\CKEditorBundle\Form\Type\CKEditorType;

    $form->add('body',  CKEditorType::Class)

After::

    <?php

    use FOS\CKEditorBundle\Form\Type\CKEditorType;

    $form->add('body',  CKEditorType::Class)

Update service definition
-------------------------

If you are fetching any of the services directly from the container you
will have to find all occurrences of ``ivory_ck_editor.*`` in your application
and replace them with ``fos_ck_editor.*``.

Instead of doing::

    $this->get('ivory_ck_editor.form.type');

You would do::

    $this-get('fos_ck_editor.form.type');


Regenerate assets
-----------------

First fetch ckeditor assets:

.. code-block:: bash

    bin/console ckeditor:install

and then regenerate Symfony assets:

.. code-block:: bash

    bin/console assets:install

.. _`SonataFormatterBundle`: https://github.com/sonata-project/SonataFormatterBundle/pull/331
