# UPGRADE

UPGRADE FROM 1.x to 2.0
=======================

Marker exception is now an interface that implements throwable.

All Model Managers have been removed.
Not used exceptions have been removed.

CKEditorType Form Type now accepts only `FOS\CKEditorBundle\Config\CKEditorConfiguration`,
and all getters and setters have been removed from the form type.

Minimum Symfony version is 3.4 and minimum php version is 7.1.

symfony/templating has been dropped along with php templates.

Twig is now a required dependency and only templating engine this library supports.

Composer Script has been removed.
