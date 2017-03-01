FROM php:latest

# APT packages
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    git \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install zip

# XDebug extensions
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && rm -rf /tmp/pear

# XDebug configuration
COPY config/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/local/bin

# Bash
RUN chsh -s /bin/bash www-data

# Workdir
WORKDIR /var/www/html

# Entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
