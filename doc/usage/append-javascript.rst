Append Custom Javascript
========================

The bundle allows you to easily append custom javascript code into
all CKEditor widgets by simply overriding the default templates. Here,
we will configure CKEditor to not remove empty span via the DTD.

Twig Template
-------------

The default Twig template is ``FOSCKEditorBundle:Form:ckeditor_widget.html.twig``.
This one has some blocks you can override according to your needs.

.. code-block:: twig

    {# app/Resources/Form/ckeditor_widget.html.twig #}
    {% extends 'FOSCKEditorBundle:Form:ckeditor_widget.html.twig' %}

    {% block ckeditor_widget_extra %}
        CKEDITOR.dtd.$removeEmpty['span'] = false;
    {% endblock %}

Then, just need to register your template as a form resources in the
configuration and it will override the default one:

.. code-block:: yaml

    # app/config/config.yml
    twig:
        form_themes:
            - "::Form/ckeditor_widget.html.twig"

PHP Template
------------

The default PHP template is ``FOSCKEditorBundle:Form:ckeditor_widget.html.php``.
This one has some slots you can override according to your needs.

.. code-block:: php

    <!-- app/Resources/views/Form/ckeditor_widget.html.php -->
    <?php $view->extend('FOSCKEditorBundle:Form:ckeditor_widget.html.php') ?>

    <?php $view['slots']->start('ckeditor_widget_extra') ?>
        CKEDITOR.dtd.$removeEmpty['span'] = false;
    <?php $view['slots']->stop() ?>

.. code-block:: yaml

    # app/config/config.yml
    framework:
        templating:
            form:
                resources:
                    - "::Form"
