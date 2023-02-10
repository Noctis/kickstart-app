FROM php:8.1-apache

RUN apt-get update && \
    apt-get install -y git libicu-dev libzip-dev unzip && \
    docker-php-ext-configure bcmath && \
    docker-php-ext-install -j$(nproc) bcmath && \
    docker-php-ext-configure intl && \
    docker-php-ext-install -j$(nproc) intl && \
    docker-php-ext-configure pdo_mysql && \
    docker-php-ext-install -j$(nproc) pdo_mysql && \
    docker-php-ext-configure zip && \
    docker-php-ext-install -j$(nproc) zip

# Uncomment the following lines to enable XDebug in PHP
#RUN pecl install xdebug && \
#    docker-php-ext-enable xdebug

COPY --from=composer /usr/bin/composer /usr/bin/composer

ENV APP_PATH /var/www/kickstart-app
ENV APP_DOCUMENT_ROOT ${APP_PATH}/public

RUN sed -ri -e 's!/var/www/html!${APP_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APP_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

WORKDIR ${APP_PATH}
