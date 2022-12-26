FROM php:8.1-apache

RUN docker-php-ext-configure pdo_mysql && \
    docker-php-ext-install -j$(nproc) pdo_mysql && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug

ENV APP_PATH /var/www/kickstart-app
ENV APP_DOCUMENT_ROOT ${APP_PATH}/public

RUN sed -ri -e 's!/var/www/html!${APP_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APP_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

WORKDIR ${APP_PATH}