FROM nginx:1.26 as nginx-dev
COPY nginx/default.conf /etc/nginx/conf.d/

CMD ["nginx", "-g", "daemon off;"]

FROM php:8.3-fpm as php-dev

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip\
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev

# Installs PHP extensions
RUN docker-php-ext-install \
    gd \
    pdo_mysql \
    opcache \
    && pecl install redis \
    && docker-php-ext-enable redis

ENV COMPOSER_ALLOW_SUPERUSER=1
ARG COMPOSER_VERSION=2.7.2
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin \
    --version=${COMPOSER_VERSION} \
    && composer clear-cache

# Expose port and start Apache server
#xdebug
ARG XDEBUG_ENABLED=false
RUN if $XDEBUG_ENABLED; then pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.max_nesting_level=1000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    ; fi

WORKDIR /var/www/html