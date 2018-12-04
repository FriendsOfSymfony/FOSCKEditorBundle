Installation
============

Download the Bundle
-------------------

Require the bundle in your ``composer.json`` file:

.. code-block:: bash

    $ composer require friendsofsymfony/ckeditor-bundle

Register the Bundle
-------------------

Then, update your ``app/AppKernel.php``:

.. code-block:: php

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                new FOS\CKEditorBundle\FOSCKEditorBundle(),
                // ...
            );

            // ...
        }
    }

Download CKEditor
-----------------

Once, you have registered the bundle, you need to install CKEditor:

If you're using Symfony <= 2.8:

.. code-block:: bash

    $ php app/console ckeditor:install

If you're using Symfony >= 3.0:

.. code-block:: bash

    $ php bin/console ckeditor:install

If you want to learn more about this command, you can read :doc:`its documentation <usage/ckeditor>`.

Install the Assets
------------------

Once, you have downloaded CKEditor, you need to install it in the web
directory.

If you're using Symfony <= 2.8:

.. code-block:: bash

    $ php app/console assets:install web

If you're using Symfony >= 3.0 without Symfony Flex:

.. code-block:: bash

    $ php bin/console assets:install web

If you're using Symfony Flex:

.. code-block:: bash

    $ php bin/console assets:install public

Configure Twig
--------------

.. note::

    This step is not required if you installed the bundle using Symfony Flex and the recipe was installed.

Finally, add some configuration under the `twig.form_themes` config key:

.. code-block:: yaml

    # Symfony 2/3: app/config/config.yml
    # Symfony 4: config/packages/twig.yaml

    twig:
        form_themes:
            - '@FOSCKEditor/Form/ckeditor_widget.html.twig'
