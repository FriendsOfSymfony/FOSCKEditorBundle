#!/usr/bin/env bash

LEVEL=${1-0}

php vendor/bin/phpstan analyse -l ${LEVEL} src tests
ARGUMENTS=${LEVEL-null}
if [ -x .travis/script_${TARGET}.sh ]; then .travis/script_${TARGET}.sh ${ARGUMENTS}; fi