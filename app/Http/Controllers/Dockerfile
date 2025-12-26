#use official php image
FROM php:8.2-apache

#set working directory
WORKDIR /var/www/html

#install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

#install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

#copy laravel project
COPY . .

#install dependencies
RUN composer install --no-dev --optimize-autoloader

#set permissions
RUN chown -R www-data:www-data /var/www/html
     && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

#expose port 9000
EXPOSE 9000

#start apache
CMD ["apache2-foreground"]