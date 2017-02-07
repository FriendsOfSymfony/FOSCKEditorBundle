#!/usr/bin/env bash

set -e

DOCKER_BUILD=${DOCKER_BUILD=false}

if [ ${DOCKER_BUILD} = false ]; then
    wget https://scrutinizer-ci.com/ocular.phar
    php ocular.phar code-coverage:upload --format=php-clover build/clover.xml
fi
