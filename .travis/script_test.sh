#!/usr/bin/env bash

FLAGS=${PHPUNIT_FLAGS-"-v --exclude-group installation"}
composer validate --strict --no-check-lock

./vendor/bin/simple-phpunit ${FLAGS}
