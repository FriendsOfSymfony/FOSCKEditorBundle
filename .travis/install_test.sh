#!/usr/bin/env bash

FLAGS=${COMPOSER_FLAGS-""}

composer remove --no-update --dev friendsofphp/php-cs-fixer

if [[ "${FLAGS}" == *"--prefer-lowest"* ]]; then
    composer update --prefer-lowest --prefer-dist --no-interaction --prefer-stable --quiet;
else
    composer update ${FLAGS} --prefer-dist --no-interaction --quiet;
fi

./vendor/bin/simple-phpunit install
