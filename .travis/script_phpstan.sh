#!/usr/bin/env bash

composer require phpstan/phpstan
php vendor/bin/phpstan analyse -l ${LEVEL} src tests -c phpstan.neon
