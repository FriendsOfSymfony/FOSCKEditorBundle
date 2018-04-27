# CHANGELOG

### 1.0.0 on FoS namespace (unreleased)

 * **2018-04-27**: Removed hhvm support, removed Symfony 3.0.* and 3.1.* support

### 6.0.2 (????-??-??)

 * aea715f - Fix CS
 * 70739bf - [Installer] Add proxy support + Rely on options resolver
 * b7ffdb0 - Typo fixed in installation guide

### 6.0.1 (2017-08-18)

 * bdd7adc - Fixed CKEditorScriptHandler::install error
 
### 6.0.0 (2017-07-14)

 * a9fe512 - [UPGRADE] Add note about CKEditor source removing
 * 4d59b54 - [Composer] Add a script handler installing CKEditor source
 * 6a4fa8b - [README] Removing Symfony version
 * 4bffdb9 - [Travis] Update docker UID
 * 9d542f6 - [Travis] Add missing Symfony versions
 * cbed42d - [Tests] Bump PHPUnit to 6.x
 * 6c3cea4 - [Doc] Fix code block
 * 83c5250 - [Doc] Fix CKEditor installation command link
 * c9dfbcc - [Doc] Fix link to requirejs
 * c86a147 - [License] Remove CKEditor source + Introduce a command to download/install it 
 
### 5.0.3 (2017-06-05)

 * 1515ca4 - Symfony Flex compatibility (namespaced syntax)
 * 7de0c4a - [Travis] Rely on trusty dist to make hhvm installable

### 5.0.2 (2017-05-20)

 * 88445d2 - Only strip assets version when the path is a directory
 * af9c05f - [Doc] Improve config documentation
 
### 5.0.1 (2017-03-01)

 * ec4b1e7 - [CKEditor] Don't alter sources

### 5.0.0 (2017-02-27)

 * 5a665e9 - [UPGRADE] Notify about PHP and Symfony bump
 * 9f0491e - Bump egeloen/json-builder to 3.0
 * e7fe834 - [README] Fix AppVeyor link
 * d75be36 - [AppVeyor] Only build master branch
 * 22a1baa - Add appveyor.yml to gitattributes
 * bd5e0a1 - Add AppVeyor support + Fix tests on windows
 * d713876 - [Composer] Bump Symfony to 2.7 + PHP to 5.6
 * add1b7a - [Travis] Replace sed by conmposer config
 * 6897fa4 - [Travis] Fix default env
 * 9d19fe2 - [Travis] Fix bash conditions
 * 6e82c3f - [Docker] Add hhvm container
 * f89d56c - Add docker support
 * f32aee0 - [Composer] Refine PHP versions
 * dddc53f - Add PHP-CS-Fixer support
 * 71393af - [Travis] Only build master branch and PRs
 * 87f7834 - [Travis] Align all Symfony deps when building
 * 86322c4 - [Travis] Add PHP 7.1 to the matrix
 * 7ab5db8 - Make AbstractTestCase abstract
 * e0f48e5 - Update CONTRIBUTING
 * eafe9e4 - Remove 4.x BC paths
 * 1acf8bc - [Scrutinizer] Fix code coverage configuration
 * 9d52b25 - Upgrade CKEditor to 4.6.2
 * 71b21ef - [License] Happy new year
 * de2097d - [Doc] Improve skin
 * 7ef1c96 - Improve toolbar management
 * 7377bec - [Travis] Upgrade matrix
 * 953e3eb - Properly destroy CKEditor instances
 * 9273300 - Automatically use the first config as default config
 
### 4.0.6 (2016-10-28)

 * 62ca1e1 - Fix usage of deprecated routing feature
 * c06af42 - Added missing configuration parameters for dedicated templates
 * 8898f62 - Update style.rst
 * e831ece - Fixed a minor syntax issue
 
### 4.0.5 (2016-09-07)

 * dde0e2c - Upgrade CKEditor to 4.5.11
 * a3554ed - Bring back form alias support for Symfony 2.8 which have been dropped accidentally

### 4.0.4 (2016-07-31)

 * 1f3f94a - [PHPUnit] Upgrade to latest version
 * 1bd768d - [Renderer] Add automatic language support
 * dfe2e05 - [Renderer] Allow to render template with an engine
 * a1daa71 - Upgrade CKEditor to 4.5.10
 * de28bd5 - [DI] Fix form alias for Symfony 2.8
 * a11ef4f - [Travis] Remove PHP 5.3.3 build
 * d2de7ea - [Doc] Update installation for Symfony >= 3
 * fa7f379 - [Doc] Update documentation for plugin configuration.
 * 900e824 - [Doc] Add section about template overriding

### 4.0.3 (2016-04-08)

 * ca4ded7 - Removes dead code which break twig template compilation
 
### 4.0.2 (2016-04-07)

 * 116b095 - Upgrade CKEditor to 4.5.8
 * b2fae58 - [Composer] Suggest egeloen/form-extra-bundle
 * db7b64d - Add custom filebrowsers support
 * 48ca6c3 - RequireJS support
 * 9c64a43 - [Routing] Fix reference type deprecation
 * ba497b4 - [Renderer] Decouple the Twig extension from the Templating component
 * 971ed32 - [Documentation] Rewrite doc using RestructuredText
 * 97c60e0 - [Template] Introduce ckeditor_widget_extra block
 * 39fd4b8 - [Configuration] Don't normalize plugins/stylesSets/templates names
 * 0e618f6 - [AssetsHelper] Fix BC layer
 * c65bfa1 - [Templating] Make Asset/Templating component optional
   
### 4.0.1 (2015-12-09)

 * bff1d04 - Fix form type tag BC layer
 
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
