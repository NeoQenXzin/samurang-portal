FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libicu-dev

RUN docker-php-ext-install pdo pdo_pgsql intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-scripts --no-autoloader
RUN composer dump-autoload --optimize
# Install Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt-get install symfony-cli

RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/var

EXPOSE 9000

CMD ["php-fpm"]