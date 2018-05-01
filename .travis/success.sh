#!/usr/bin/env bash

set -e

COVERAGE=${COVERAGE-false}

if [ "${COVERAGE}" == true ]; then
    # Coveralls client install
    wget https://github.com/satooshi/php-coveralls/releases/download/v1.0.1/coveralls.phar --output-document="${HOME}/bin/coveralls"
    chmod u+x "${HOME}/bin/coveralls"

    coveralls -v
fi;
