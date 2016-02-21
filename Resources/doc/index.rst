Getting started with IvoryCKEditorBundle
========================================

.. toctree::
    :hidden:

    installation
    usage/index

Overview
--------

The bundle integrates `CKEditor`_ into `Symfony`_ via the `Form Component`_. It
automatically registers a new type called ``ckeditor`` which can be easily as
well as highly configured. This type extends the `textarea`_ one, meaning all
textarea options are available.

Here, an example where we customize the `CKEditor config`_:

.. code-block:: php

    $builder->add('field', 'ckeditor', array(
        'config' => array(
            'uiColor' => '#ffffff',
            //...
        ),
    ));

.. note::

    As of Symfony 2.8, the second argument of ``$builder->add()`` accepts the
    fully qualified class name of the form type and it becomes mandatory to use
    it in Symfony 3.0. Depending on your Symfony versions, you should replace
    the ``ckeditor`` parameter by
    ``Ivory\CKEditorBundle\Form\Type\CKEditorType`` or by
    ``CKEditorType::class`` if your project relies on PHP 5.5+.

Installation
------------

To install the bundle, please, read the :doc:`Installation documentation <installation>`.

Usage
-----

If you want to learn more, this documentation covers the following use cases:

.. include:: usage/index.rst.inc

.. _`CKEditor`: http://ckeditor.com/
.. _`Symfony`: http://symfony.com/
.. _`Form Component`: http://symfony.com/doc/current/book/forms.html
.. _`textarea`: http://symfony.com/doc/current/reference/forms/types/textarea.html
.. _`CKEditor config`: http://docs.ckeditor.com/#!/api/CKEDITOR.config
