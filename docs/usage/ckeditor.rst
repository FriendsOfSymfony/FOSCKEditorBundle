CKEditor Installation
=====================

The CKEditor source is not shipped with the bundle due to license restriction
(GPL, LGPL and MPL) whereas the bundle relies on the MIT one which are not
compatible together. To install CKEditor source, you can use the built-in
Symfony command.

Composer Script
---------------

The easiest way to manage CKEditor installation and update is to integrate it
at the middle of your composer routine (after the cache clear but before the
assets installation).

.. code-block:: json

    {
        "scripts": {
            "auto-scripts": {
                "cache:clear": "symfony-cmd",
                "ckeditor:install --clear=drop": "symfony-cmd",
                "assets:install --symlink --relative %PUBLIC_DIR%": "symfony-cmd"
            },
            "post-install-cmd": [
                "@auto-scripts"
            ],
            "post-update-cmd": [
                "@auto-scripts"
            ]
        }
    }

Symfony Command
---------------

.. code-block:: bash

    $ php bin/console ckeditor:install

By default, the command downloads the latest CKEditor full release (samples
directory excluded) in the ``Resource/public`` directory of the bundle. Most of
the time, this is exactly what you want but the command allows you to do more.

Download Path
~~~~~~~~~~~~~

If you don't want to download CKEditor in the ``Resource/public`` directory of
the bundle, you can use a custom path (absolute):

.. code-block:: bash

    $ php bin/console ckeditor:install /var/www/html/web/ckeditor

CKEditor Release
~~~~~~~~~~~~~~~~

You can choose which CKEditor release (full, standard or basic) to download:

.. code-block:: bash

    $ php bin/console ckeditor:install --release=basic

CKEditor Custom Build
~~~~~~~~~~~~~~~~~~~~~

It's also possible to use custom build generated using CKEditor online builder:
https://ckeditor.com/cke4/builder. Download ZIP archive from CKEditor website
and use your custom build ID from `build-config.js` file:

.. code-block:: bash

    $ php bin/console ckeditor:install --release=custom --custom-build-id=574a82a0d3e9226d94b0e91d10eaa372

CKEditor Version
~~~~~~~~~~~~~~~~

If your want a specific CKEditor version, you can use:

.. code-block:: bash

    $ php bin/console ckeditor:install --tag=4.6.0

Silence Progress bars
~~~~~~~~~~~~~~~~~~~~~~~~~~~

While downloading files necessary for the install, a progress bar will be shown
by default, if you would prefer hiding it, use:

.. code-block:: bash

    $ php bin/console ckeditor:install --no-progress-bar

Clear Previous Installation
~~~~~~~~~~~~~~~~~~~~~~~~~~~

By default, the command will ask you what to do when there is a previous CKEditor
installation detected but in non interactive mode, you can control automatically
how to handle such case:

.. code-block:: bash

    $ php bin/console ckeditor:install --clear=drop
    $ php bin/console ckeditor:install --clear=keep
    $ php bin/console ckeditor:install --clear=skip

 - ``drop``: Drop the previous installation & install.
 - ``keep``: Keep the previous installation & install by overriding files.
 - ``skip``: Keep the previous installation & skip install.

Path Exclusion
~~~~~~~~~~~~~~

When extracting the downloaded CKEditor ZIP archive, you can exclude paths
such as samples, adapters, whatever:

.. code-block:: bash

    $ php bin/console ckeditor:install --exclude=samples --exclude=adapters

Proxy
~~~~~

If you're using a proxy, you can use the following environment variables:

.. code-block:: bash

    $ export HTTP_PROXY=http://127.0.0.1:8080
    $ export HTTPS_PROXY=http://127.0.0.1:8080

You can also define if the request URI should be full with:

.. code-block:: bash

    $ export HTTP_PROXY_REQUEST_FULLURI=true
    $ export HTTPS_PROXY_REQUEST_FULLURI=true

Reminder
~~~~~~~~

The command has been well documented, if you want to check it out:

.. code-block:: bash

    $ php bin/console ckeditor:install --help
