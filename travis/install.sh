#!/usr/bin/env bash

set -e

SYMFONY_VERSION=${SYMFONY_VERSION-2.3.*}
COMPOSER_PREFER_LOWEST=${COMPOSER_PREFER_LOWEST-false}
DOCKER_BUILD=${DOCKER_BUILD=false}

if [ ${DOCKER_BUILD} = true ]; then
    cp .env.dist .env

    docker-compose build
    docker-compose run --rm php composer update --prefer-source

    exit
fi

composer self-update

composer require --no-update symfony/framework-bundle:${SYMFONY_VERSION}
composer require --no-update symfony/form:${SYMFONY_VERSION}
composer require --no-update --dev symfony/templating:${SYMFONY_VERSION}
composer require --no-update --dev symfony/twig-bridge:${SYMFONY_VERSION}
composer require --no-update --dev symfony/yaml:${SYMFONY_VERSION}

if [[ "$SYMFONY_VERSION" =~ ^2\.[2-6] ]]; then
    composer require --no-update --dev symfony/asset:2.7.*
else
    composer require --no-update --dev symfony/asset:${SYMFONY_VERSION}
fi

composer remove --no-update --dev friendsofphp/php-cs-fixer

if [[ "$SYMFONY_VERSION" = *dev* ]]; then
    sed -i "s/\"MIT\"/\"MIT\",\"minimum-stability\":\"dev\"/g" composer.json
fi

composer update --prefer-source `if [[ ${COMPOSER_PREFER_LOWEST} = true ]]; then echo "--prefer-lowest --prefer-stable"; fi`
