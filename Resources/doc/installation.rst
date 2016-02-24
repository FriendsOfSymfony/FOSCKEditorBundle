Installation
============

Download the Bundle
-------------------

Require the bundle in your ``composer.json`` file:

.. code-block:: bash

    $ composer require egeloen/ckeditor-bundle

Register the Bundle
-------------------

Then, update your ``app/AppKernel.php``:

.. code-block:: php

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
                // ...
            );

            // ...
        }
    }

Install the Assets
------------------

Once, you have registered the bundle, you need to install the assets in the web
directory:

.. code-block:: bash

    $ php app/console assets:install web
