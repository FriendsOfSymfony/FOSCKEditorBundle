#!/usr/bin/env bash

composer require phpstan/phpstan
php vendor/bin/phpstan analyse -l ${LEVEL} src -c phpstan.neon
