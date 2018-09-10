# UPGRADE

UPGRADE FROM 1.x to 2.0
=======================

CKEditorType Form Type now has only 1 parameter in constructor that represents
config taken from bundle configuration, and all getters and setters have been
removed.

Minimum Symfony version is 3.4 and minimum php version is 7.1.

symfony/templating has been dropped along with php templates.

Twig is now a required dependency and only templating engine this library supports.

Composer Script has been removed.