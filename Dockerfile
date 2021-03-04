FROM php:7.4.8-apache
RUN apt-get update && apt-get install -y \
    && docker-php-ext-install pdo pdo_mysql
EXPOSE 80
WORKDIR /app
COPY . /app
COPY vhost.conf /etc/apache2/sites-available/000-default.conf
RUN chown -R www-data:www-data /app \
    && a2enmod rewrite
