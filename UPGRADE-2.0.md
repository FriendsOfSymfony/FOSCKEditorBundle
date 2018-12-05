# UPGRADE

UPGRADE FROM 1.x to 2.0
=======================

Added typehints and return types.

Removed nullable constructor arguments on most services.

Classes are now final.

Marker exception is now an interface that implements throwable.

All Model Managers have been removed.
Not used exceptions have been removed.

CKEditorType Form Type now accepts only 1 argument
of type `FOS\CKEditorBundle\Config\CKEditorConfigurationInterface`.

All getters and setters have been removed from the CKEditorType Form Type.

Minimum Symfony version is 3.4 and minimum php version is 7.1.

symfony/templating has been dropped along with php templates.

Twig is now a required dependency and only templating engine this library supports.

Composer Script has been removed.

To make Twig render the editors, you must add some configuration under the `twig.form_themes` config key:

```yaml
# Symfony 2/3: app/config/config.yml
# Symfony 4: config/packages/twig.yaml

twig:
    form_themes:
        - '@FOSCKEditor/Form/ckeditor_widget.html.twig'
```
