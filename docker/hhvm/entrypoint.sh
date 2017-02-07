#!/usr/bin/env bash

set -e

GROUP_ID=${GROUP_ID-1000}
USER_ID=${USER_ID-1000}
XDEBUG=${XDEBUG-0}

# Enable XDebug
if [ ${XDEBUG} = 1 ]; then
    cat /var/www/xdebug.ini >> /etc/hhvm/php.ini
fi

# Remove XDebug temporary configuration
rm -f /var/www/xdebug.ini

# Permissions
groupmod -g ${GROUP_ID} www-data
usermod -u ${USER_ID} www-data

# Start bash or forward command
if [ "$1" = "bash" ]; then
    su www-data
else
    su www-data -c "$*"
fi
