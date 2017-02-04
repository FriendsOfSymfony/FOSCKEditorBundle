# Contributing

If you're here, you would like to contribute to this repository and you're really welcome!

## Coding standard

This repository follows the [PSR-2 standard](http://www.php-fig.org/psr/psr-2/) and so, if you want to contribute,
you must follow these rules.

## Feature request

If you think a feature is missing, please report it or even better implement it :). If you report it, describe the more
precisely what you would like to see implemented and we will discuss what is the best approach for it. If you can do
some search before submitting it and link the resources to your description, you're awesome! It will allow me to more
easily understood/implement it.

## Bug report

If you think you have detected a bug or a doc issue, please report it or even better fix it :). If you report it,
please be the more precise possible. Here a little list of required informations:

 * Symfony-standard fork which reproduces the bug.
 * Precise description of the bug.
 * Symfony version used.
 * Bundle version used.

## Bug fix

If you're here, you are going to fix a bug and you're the best! To do it, first fork the repository, clone it and
create a new branch with the following commands:

``` bash
$ git clone git@github.com:your-name/IvoryCKEditorBundle.git
$ git checkout -b bug-fix-description
```

Then, we recommend to use [Docker](https://www.docker.com) and follow this [instructions](/Resources/doc/docker.rst) in
order to set up the project.

When you're on the new branch with the dependencies, code as much as you want and when the fix is ready, don't commit
it immediately. Before, you will need to add tests and update the doc. For the tests, everything is tested with
[PHPUnit](http://phpunit.de/) and the doc is in the markdown format under the `Resources/doc` directory.

Then, when you have fixed the bug, tested it and documented it, you can commit and push it with the following commands:

``` bash
$ git commit -m "Bug fix description"
$ git push origin bug-fix-description
```

If you have reworked you patch, please squash all your commits in a single one with the following commands (here, we
will assume you would like to squash 3 commits in a single one):

``` bash
$ git rebase -i HEAD~3
```

If your branch conflicts with the master branch, you will need to rebase and repush it with the following commands:

``` bash
$ git remote add upstream git@github.com:egeloen/IvoryCKEditorBundle.git
$ git pull --rebase upstream master
$ git push origin bug-fix-description -f
```
