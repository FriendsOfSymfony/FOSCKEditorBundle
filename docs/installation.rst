Installation
============

Download the Bundle
-------------------

Require the bundle in your ``composer.json`` file:

.. code-block:: bash

    $ composer require friendsofsymfony/ckeditor-bundle

Register the Bundle
-------------------

If you're using Symfony >= 4.0, skip this step, as it is automatically done by Flex's recipe.

If you choose to not execute the recipe, and if you're using Symfony >= 4.0, update your ``config/bundles.php``:

.. code-block:: php

    return [
        // ...
        FOS\CKEditorBundle\FOSCKEditorBundle::class => ['all' => true],
    ];

If you're using Symfony < 4.0, update your ``app/AppKernel.php``:

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
With bundle's command
~~~~~~~~~~~~~~~~~~~~~

Once, you have registered the bundle, you need to install CKEditor:

If you're using Symfony <= 2.8:

.. code-block:: bash

    $ php app/console ckeditor:install

If you're using Symfony >= 3.0:

.. code-block:: bash

    $ php bin/console ckeditor:install

If you want to learn more about this command, you can read :doc:`its documentation <usage/ckeditor>`.

Using Webpack Encore
~~~~~~~~~~~~~~~~~~~~

If you have installed Webpack Encore, you may want to have it as a `node_module` dependency.

You can by running this command:

.. code-block:: bash

    # if you are using NPM as package manager
    $ npm install --save ckeditor@^4.13.0

    # if you are using Yarn as package manager
    $ yarn add ckeditor@^4.13.0

Once installed, add the following lines to your Webpack Encore configuration file (this excludes the samples directory from the ckeditor node module):

.. code-block:: javascript

    // webpack.config.js
    var Encore = require('@symfony/webpack-encore');

    Encore
        // ...
        .copyFiles([
            {from: './node_modules/ckeditor4/', to: 'ckeditor/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
            {from: './node_modules/ckeditor4/adapters', to: 'ckeditor/adapters/[path][name].[ext]'},
            {from: './node_modules/ckeditor4/lang', to: 'ckeditor/lang/[path][name].[ext]'},
            {from: './node_modules/ckeditor4/plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
            {from: './node_modules/ckeditor4/skins', to: 'ckeditor/skins/[path][name].[ext]'},
            {from: './node_modules/ckeditor4/vendor', to: 'ckeditor/vendor/[path][name].[ext]'}
        ])
        // Uncomment the following line if you are using Webpack Encore <= 0.24
        // .addLoader({test: /\.json$/i, include: [require('path').resolve(__dirname, 'node_modules/ckeditor')], loader: 'raw-loader', type: 'javascript/auto'})
    ;

Then, override the bundle's configuration to point to the new CKEditor path:

.. code-block:: yaml

    fos_ck_editor:
        # ...
        base_path: "build/ckeditor"
        js_path:   "build/ckeditor/ckeditor.js"

Finally, run encore command:

.. code-block:: bash

    # if you are using NPM as package manager
    $ npm run dev

    # if you are using Yarn as package manager
    $ yarn run encore dev


Install the Assets
------------------

.. note::

    This step is not required if you are using Webpack Encore.

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

Finally, add some configuration under the ``twig.form_themes`` config key:

.. code-block:: yaml

    # Symfony 2/3: app/config/config.yml
    # Symfony 4: config/packages/twig.yaml

    twig:
        form_themes:
            - '@FOSCKEditor/Form/ckeditor_widget.html.twig'
