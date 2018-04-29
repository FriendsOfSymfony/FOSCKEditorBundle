#!/usr/bin/env sh

composer remove --no-update --dev friendsofphp/php-cs-fixer

if [[ "$COMPOSER_FLAGS" == *"--prefer-lowest"* ]]; then composer update --prefer-dist --no-interaction --prefer-stable --quiet; fi

composer update ${COMPOSER_FLAGS} --prefer-dist --no-interaction

./vendor/bin/simple-phpunit install
