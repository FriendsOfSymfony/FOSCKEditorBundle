Style support
=============

The bundle allows you to define your own styles. Like plugins, you can define
them globally in your configuration:

.. code-block:: yaml

    # app/config/config.yml
    fos_ck_editor:
        default_config: my_config
        configs:
            my_config:
                stylesSet: "my_styles"
        styles:
            my_styles:
                - { name: "Blue Title", element: "h2", styles: { color: "Blue" }}
                - { name: "CSS Style", element: "span", attributes: { class: "my_style" }}
                - { name: "Widget Style", type: widget, widget: "my_widget", attributes: { class: "my_widget_style" }}

Or you can define them in your widget:

.. code-block:: php

    $builder->add('field', 'ckeditor', array(
        'config' => array(
            'stylesSet' => 'my_styles',
        ),
        'styles' => array(
            'my_styles' => array(
                array('name' => 'Blue Title', 'element' => 'h2', 'styles' => array('color' => 'Blue')),
                array('name' => 'CSS Style', 'element' => 'span', 'attributes' => array('class' => 'my_style')),
                array('name' => 'Multiple Element Style', 'element' => array('h2', 'span'), 'attributes' => array('class' => 'my_class')),
                array('name' => 'Widget Style', 'type' => 'widget' , 'widget' => 'my_widget', 'attributes' => array('class' => 'my_widget_style')),
            ),
        ),
    ));
