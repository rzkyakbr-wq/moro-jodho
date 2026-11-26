FROM php:8.2-apache
RUN apt-get update && apt-get install -y libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite
RUN a2enmod rewrite
COPY . /var/www/html
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache