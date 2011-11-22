Provides a CKEditor integration for your Symfony2 Project.

This bundle adds the form field type ``ckeditor`` to the Form Component.

Actually, it allows you to manage:

   - toolbar
   - uiColor

Documentation
-------------

   1. [Installation](http://github.com/egeloen/IvoryCKEditorBundle/blob/master/Resources/doc/installation.md)
   2. [Usage](http://github.com/egeloen/IvoryCKEditorBundle/blob/master/Resources/doc/usage.md)
   3. [Test](http://github.com/egeloen/IvoryCKEditorBundle/blob/master/Resources/doc/test.md)

Contribute
----------

If the bundle doesn't allow you to customize an option, I invite you to make a PR & I will merge it.

To add an option, it pretty easy. You just have to add it to the ``Ivory\CKEditorBundle\Form\Type\CKEditorType`` & update the generated javascript in the ``IvoryCKEditorBundle:Form:ckeditor_widget.html.twig`` template.

License
-------

This bundle is under the MIT license. See the complete license [here](http://github.com/egeloen/IvoryCKEditorBundle/blob/master/Resources/meta/LICENSE).
