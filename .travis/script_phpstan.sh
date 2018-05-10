#!/usr/bin/env bash

php vendor/bin/phpstan analyse -l ${LEVEL} src tests -c phpstan.neon
