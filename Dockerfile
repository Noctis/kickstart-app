FROM php:8-apache

ENV APP_PATH /var/www/kickstart-app
ENV APP_DOCUMENT_ROOT ${APP_PATH}/public

RUN sed -ri -e 's!/var/www/html!${APP_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APP_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR ${APP_PATH}
