Docker
======

The most easy way to set up the project is to install `Docker`_ and `Docker Compose`_ and build the project.

Configure
---------

The configuration is shipped with a distribution environment file allowing you to customize your IDE and XDebug
settings as well as your current user/group ID:

.. code-block:: bash

    $ cp .env.dist .env

.. note::

    The most important part is the `USER_ID` and `GROUP_ID` which should match your current user/group.

Build
-----

Once you have configured your environment, you can build the project:

.. code-block:: bash

    $ docker-compose build

Composer
--------

Install the dependencies via `Composer`_:

.. code-block:: bash

    $ docker-compose run --rm php composer install

Tests
-----

To run the test suite, you can use:

.. code-block:: bash

    $ docker-compose run --rm php vendor/bin/phpunit

If you want to run the test suite against `HHVM`_, you can use:

.. code-block:: bash

    $ docker-compose run --rm hhvm vendor/bin/phpunit

XDebug
------

If you want to use XDebug, make sure you have fully configured your `.env` file and use:

.. code-block:: bash

    $ docker-compose run --rm -e XDEBUG=1 php vendor/bin/phpunit

.. _`Composer`: https://getcomposer.org/
.. _`Docker`: https://www.docker.com
.. _`Docker Compose`: https://docs.docker.com/compose/docker.rst
.. _`HHVM`: http://hhvm.com/
