#!/usr/bin/env bash

set -e

COVERAGE=${COVERAGE-false}

if [ "${COVERAGE}" == true ]; then
    wget https://scrutinizer-ci.com/ocular.phar
    php ocular.phar code-coverage:upload --format=php-clover build/clover.xml
fi;
