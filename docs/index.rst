Getting started with IvoryCKEditorBundle
========================================

.. toctree::
    :hidden:

    installation
    usage/index
    docker

Overview
--------

The bundle integrates `CKEditor`_ into `Symfony`_ via the `Form Component`_. It
automatically registers a new type called ``ckeditor`` which can be fully
configured. This type extends the `textarea`_ one, meaning all textarea options
are available.

Here, an example where we customize the `CKEditor config`_:

.. code-block:: php

    // Symfony 2.7 and previous versions
    $builder->add('field', 'ckeditor', array(
        'config' => array(
            'uiColor' => '#ffffff',
            //...
        ),
    ));

    // Symfony 2.8 and newer versions
    use Ivory\CKEditorBundle\Form\Type\CKEditorType;

    $builder->add('field', CKEditorType::class, array(
        'config' => array(
            'uiColor' => '#ffffff',
            //...
        ),
    ));

.. note::

    If you're using PHP < 5.5 and Symfony 2.8+, you must rely on
    ``Ivory\CKEditorBundle\Form\Type\CKEditorType`` instead of
    ``CKEditorType::class`` as this constant does not exist.

Installation
------------

To install the bundle, please, read the :doc:`Installation documentation <installation>`.

Usage
-----

If you want to learn more, this documentation covers the following use cases:

.. include:: usage/index.rst.inc

Contributing
------------

To set up the bundle, please, read the :doc:`Docker documentation <docker>`.

.. _`CKEditor`: http://ckeditor.com/
.. _`Symfony`: http://symfony.com/
.. _`Form Component`: http://symfony.com/doc/current/book/forms.html
.. _`textarea`: http://symfony.com/doc/current/reference/forms/types/textarea.html
.. _`CKEditor config`: http://docs.ckeditor.com/#!/api/CKEDITOR.config
