Getting started with FOSCKEditorBundle
========================================

.. toctree::
    :hidden:

    installation
    migration
    usage/index

Overview
--------

The bundle integrates `CKEditor`_ into `Symfony`_ via the `Form Component`_. It
automatically registers a new type called ``ckeditor`` which can be fully
configured. This type extends the `textarea`_ one, meaning all textarea options
are available.

Here, an example where we customize the `CKEditor config`_::

    use FOS\CKEditorBundle\Form\Type\CKEditorType;

    $builder->add('field', CKEditorType::class, array(
        'config' => array(
            'uiColor' => '#ffffff',
            //...
        ),
    ));

Installation
------------

To install the bundle, please, read the :doc:`Installation documentation <installation>`.

Migration from IvoryCKEditorBundle to FOSCKEditorBundle
-------------------------------------------------------

As IvoryCKEditorBundle was abandoned, FriendsOfSymfony took this bundle over, to help
you easily migrate :doc:`just follow our guide <migration>`.

Usage
-----

If you want to learn more, this documentation covers the following use cases:

.. include:: usage/index.rst.inc

Contributing
------------

.. _`CKEditor`: http://ckeditor.com/
.. _`Symfony`: http://symfony.com/
.. _`Form Component`: http://symfony.com/doc/current/book/forms.html
.. _`textarea`: http://symfony.com/doc/current/reference/forms/types/textarea.html
.. _`CKEditor config`: http://docs.ckeditor.com/#!/api/CKEDITOR.config
