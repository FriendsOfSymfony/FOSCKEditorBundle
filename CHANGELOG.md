# CHANGELOG

### 4.0.0 (2015-12-06)

 * 147166c - Update CKEditor to 4.5.5
 * 9e4ae7e - Make code base compatible with Symfony3
 * 6b7eb55 - Add disable auto inline support
 * d69047f - [Travis] Add PHP 7 + Symfony 2.8.*@dev
 * 02d8927 - Fix manager services infected by local configs
 * 0d8ede1 - [Doc] Update installation
 * afea981 - Update CKEditor to 4.5.2
 * f80cea0 - Updated CKEditor to 4.5.1
 * 29f34a8 - Removed excessive use off long variable names.
 * 7ee6d18 - [Template] Wrap widget code in $(document).ready() if jquery is enabled

### 3.0.1 (2015-03-08)

 * 982342e - Move resources merging in a compiler pass
 * 838a249 - [Form][Type] Add BC layer for setDefaultOptions
 * e651f63 - Use assets.packages instead of templating.helper.assets
 * c0da2ac - Use new way of adding allowed types for symfony >= 2.6
 * 9f3f97c - [Composer] Allow egeloen/json-builder 1.0.*
 * 8f720d0 - Upgrade CKEditor to 4.4.7

### 3.0.0 (2014-12-29)

 * cbad747 - Add inline editing support
 * c875b8b - [Travis] Add composer prefer-lowest build
 * b23b5d1 - [Travis] Move Symfony 2.6.*@dev to 2.6.*
 * c6c5dbe - Add stylesSet element array support
 * c573a39 - Add JQuery adapter support
 * 4035ca0 - Upgrade CKEditor to 4.4.6
 * 280abf1 - [Travis] Update config
 * 00086ab - Add .gitattributes
 * 3c82c18 - [Readme] Fix badge uri
 * 6feb086 - [DependencyInjection] Remove *.class parameters
 * 588ffa3 - [Test] Remove bootstrap.php
 * cc0a387 - [Encapsulation] Move everything from protected to private (except for entry point)
 * 03045fd - [Form] Add input_sync option

### 2.5.2 (2014-11-08)

 * 900d69b - [Config] Fix language conversion
 * 133bcfc - [Doc] Link to fmelfinder doc
 * e8a41a4 - Add type and widget options for styles

### 2.5.1 (2014-10-30)

 * 3b43d72 - Upgrade CKEditor to 4.4.5
 * 6be9ee8 - [Build] Move CKEditor sync script from Resources/build/sync.sh to bin/ckeditor-sync

### 2.5.0 (2014-08-29)

 * 24e81ff - [Gitignore] Remove Composer installer and phar
 * 47cdde6 - [README] Rely on relative link
 * ce5813f - Add contributing doc
 * 35baf4c - [Composer] Refine deps
 * ba8b84a - Upgrade CKEditor to 4.4.4
 * b9f21fa - [Config] Fix stylesSet YAML keys normalization
 * e47d803 - [Travis] Improve build matrix
 * f785de0 - Allow to disable the CKEditor library loading
 * c43e326 - [Helper] Revert only load the CKEditor library one time

### 2.4.0 (2014-07-15)

 * c9fdfbe - [Resources] Upgrade CKEditor to 4.4.3
 * b2d2abc - [README] Add versioneye badge
 * 7bd8f86 - [Resources] Upgrade CKEditor to 4.4.2
 * 5e8fc10 - [Templating] Move the CKEditorHelper class from the Helper to the Templating namespace
 * cc711dd - [Helper] Merge the AssetsVersionTrimerHelper into the CKEditorHelper
 * c24f3c4 - [README] Add packagist badges
 * 843f88d - [Test] Fix PHP template engine setup for HHVM
 * bdfa451 - [Travis] Increase build matrix
 * 05f36a5 - [Composer] Bump PHP to 5.3.3 + Twig to 1.12 + PHPUnit to 4.0
 * b3b7a23 - [Twig] Proxify helper calls on the extension
 * cd4099b - [Travis][Composer] Remove --dev
 * 1ca9425 - [Composer] Clean suggest section
 * cb750c9 - [Composer] Upgrade to PSR-4

### 2.3.2 (2014-06-17)

 * 88b21ea - [Travis] Add Symfony 2.5 + Remove 2.0 branch
 * 5793ab2 - [Helper] Fix test according to Symfony
 * ea39ae1 - [DependencyInjection] Prepend resources instead of append them
 * 7d6f016 - [Test] Move fixtures at the root test directory
 * 805161b - [Model] Improve interface PHPDoc
 * 8e1cb8d - [Test] Fix PHPDoc
 * e5aeb4c - [README] Add Scrutunizer CI badge
 * ccfd632 - [Helper] Refactor CKEditorHelper::renderReplace for better comprehension
 * fb0c5e5 - Fix PHPDoc + CS
 * 1ef08af - [DependencyInjection] Refactor extension for a better comprehension
 * d8cef6f - [DependencyInjection] Split configuration
 * 6c10e68 - [DependencyInjection] Rely on ConfigurableExtension

### 2.3.1 (2014-05-26)

 * 478c4ed - Fix CKEditor target branch
 * b430689 - Upgrade CKEditor to 4.4.1

### 2.3.0 (2014-05-16)

 * 4fb29d1 - [Helper] Only load the CKEditor library one time
 * 41636f9 - Add coveralls support
 * 13e7038 - Allow RegExp by relying on egeloen/json-builder
 * ac6db2a - Upgrade CKEditor to 4.4.0
 * 648aa63 - [Helper] Only render StylesSet if they are not already registered

### 2.2.1 (2014-01-31)

 * aa81171 - [Travis] Make symfony/form dynamic
 * e51427c - [Twig] Fix js escaping
 * fa08cd3 - [Twig] Fix caching by lazy loading services scoped request

### 2.2.0 (2014-01-30)

 * db93af5 - [Model] Move all view logic to an helper
 * ff12310 - Upgrade CKEditor to 4.3.2
 * 2b1786a - Update new year
 * cdad813 - Deprecate Symfony 2.0

### 1.1.9 - 2.1.9 (2014-01-04)

 * ebeb553 - [ConfigManager] Fix merge config behavior
 * 4808b41 - Fix Config, Plugin, Template & StylesSet arrays initialization
 * ec2f56d - [Template] Fix textarea value escaping
 * 58f9549 - [ConfigManager] Allow to define filebrowser*Url via a closure
 * 9ecc2c1 - [Model] Add stylesSet support
 * c199353 - [Model] Add templates support

### 1.1.8 - 2.1.8 (2013-12-12)

 * b22d11c - Upgrade CKEditor to 4.3.1
 * c9e65d7 - [Travis] Simplify matrix + Add Symfony 2.4 to the build
 * 434e92f - [Type] Add CKEditor constants support
 * af8f9da - Upgrade CKEditor to 4.3

### 1.1.7 - 2.1.7 (2013-10-09)

 * 03f90cf - Upgrade CKEditor to 4.2.1
 * 4a37ad8 - [Doc] SonataMedia integration
 * a94df4f - [DependencyInjection] Introduce built-in toolbars (basic, standard, full)
 * bcae378 - [Doc] Fix FMElfinderBundle integration example

### 1.1.6 - 2.1.6 (2013-08-22)

 * 992c7df - [Form] Simplify default configuration handling
 * 8c085cc - [Doc] Add FMElfinderBundle documentation
 * a8a9a7e - [Form] Allow required html attribute

### 1.1.5 - 2.1.5 (2013-07-18)

 * 3bfbc01 - Upgrade CKEditor to 4.2
 * 099bf82 - [Composer] Add branch alias
 * c04e2ed - [Twig] Don't escape textarea value
 * ecef869 - Add default configuration support

### 1.1.4 - 2.1.4 (2013-06-17)

 * 43d2675 - Upgrade CKEditor to 4.1.1
 * cb9598b - [Travis] Use --prefer-source to avoid random build fail
 * 4da8e71 - PSR2 compatibility
 * 133ef7b - [Composer] Add PHPUnit in require-dev & use it in travis

### 1.1.3 - 2.1.3 (2013-03-18)

 * 464fd64 - Add PHP templating engine support
 * eb7c407 - Remove trim asset version twig extension & use the service instead
 * 3634c65 - Allow to use custom CKEditor versions
 * cca336a - Extract assets version trim logic in a dedicated service
 * 2093bcb - [Type] Allow to disable CKEditor at the widget level
 * c1a89c3 - [PluginManager] Refactor to handle assets support
 * 4250a92 - [ConfigManager] Fix contentsCss if the application does not live at the root of the host
 * a2384c7 - Fix CKEditor destruction when it is loaded multiple times by AJAX (Sonata compatibility)
 * de8073f - Upgrade CKEditor to 4.0.2
 * 861d418 - Allow to disable ckeditor widget for testing purpose
 * ec29bfb - [Build] Add bash script to sync stable CKEditor release

### 1.1.2 - 2.1.2 (2013-02-19)

 * a6b1556 - Add plugins support
 * c796be2 - Normalize line endings
 * d078d28 - Handle filebrowser URL generation

### 1.1.1 - 2.1.1 (2013-01-27)

 * e0b086a - Allow to configure ckeditor form type through configuration
 * 038d7c1 - Upgrade CKEditor to 4.0.1
 * b90ea78 - Fix assets_version support
 * 4be2e56 - Add support for assets_version
 * e787087 - [Widget] Remove autoescape js

### 1.1.0 - 2.1.0 (2013-01-12)

 * fd79848 - [Form][Type] Allow to set all config options.

### 1.0.0 - 2.0.0 (2013-01-12)
