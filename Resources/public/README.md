CKEditor 4 - Releases
=====================

## Releases Code

This repository contains the official release versions of CKEditor.

There are three versions for each release - [basic, standard and full](http://ckeditor.com/demo#toolbar).
They differ by the number of compiled in plugins and configuration of the toolbar.

All versions available in this repository have been built using
[CKBuilder](http://ckeditor.com/builder), so they are ready
to be used in production environment.

## Documentation

The full editor documentation is available online at the following address:
http://docs.ckeditor.com

## Installation

To install one of available releases, just clone this repository and
switch to corresponding branch (see next section):

	git clone git://github.com/ckeditor/ckeditor-releases.git
	git co <release branch>

If you're using git for your project and you want to integrate CKEditor,
we recommend you adding this repository as a
[submodule](http://git-scm.com/book/en/Git-Tools-Submodules).

	git submodule add git://github.com/ckeditor/ckeditor-releases.git <clone dir>
	cd <clone dir>
	git co <release branch>
	cd <your repository dir>
	git commit -m "Added CKEditor submodule in <clone dir> directory."

### Available Branches

This repository contains the following branches:

  - **stable/(basic|standard|full)**: latest stable release tag point (non-beta).
  - **latest/(basic|standard|full)**: latest release tag point (including betas).
  - **A.B.x/(basic|standard|full)** (e.g. 4.0.x/basic): release freeze, tests and tagging. Hotfixing.

Additionally, all releases will have their relative tags in this form: 4.0, 4.0.1, etc.
For example: **4.0/(basic|standard|full)**.

## Checking Your Installation

The editor comes with a few sample pages that can be used to verify that
installation proceeded properly. Take a look at the `samples` directory.

To test your installation, just call the following page at your website:

	http://<your site>/<CKEditor installation path>/samples/index.html

For example:

	http://www.example.com/ckeditor/samples/index.html

### License

Licensed under the GPL, LGPL and MPL licenses, at your choice.

For full details about license, please check the LICENSE.md file.
