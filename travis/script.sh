#!/usr/bin/env bash

set -e

TRAVIS_PHP_VERSION=${TRAVIS_PHP_VERSION-5.6}
DOCKER_BUILD=${DOCKER_BUILD-false}

if [ "$DOCKER_BUILD" = false ]; then
    vendor/bin/phpunit `if [ ! "$TRAVIS_PHP_VERSION" = "hhvm" ]; then echo "--coverage-clover build/clover.xml"; fi`
fi

if [ "$DOCKER_BUILD" = true ]; then
    docker-compose run --rm php vendor/bin/phpunit
    docker-compose run --rm hhvm vendor/bin/phpunit
fi
