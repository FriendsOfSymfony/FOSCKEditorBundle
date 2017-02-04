FROM php:latest

# APT packages
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    git \
    dos2unix \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install zip

# XDebug extensions
RUN pecl install xdebug && rm -rf /tmp/pear
COPY config/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Bash
RUN chsh -s /bin/bash www-data

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/local/bin

# Workdir
WORKDIR /var/www/html

# Entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
