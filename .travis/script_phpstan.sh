#!/usr/bin/env bash

LEVEL=${1-0}

php vendor/bin/phpstan analyse -l ${LEVEL} src tests
