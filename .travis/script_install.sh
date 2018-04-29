#!/usr/bin/env sh

composer validate --strict --no-check-lock

./vendor/bin/simple-phpunit ${PHPUNIT_FLAGS}
