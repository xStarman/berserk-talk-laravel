FROM php:7.4-apache
WORKDIR /var/www/html
RUN mkdir application
RUN apt-get update
WORKDIR /var/www/html/application
RUN apt-get update && apt-get install -y git curl libpng-dev libonig-dev libxml2-dev zip unzip
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN sed -ri -e 's!/var/www/html!/var/www/html/application/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/application/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite
RUN service apache2 restart
COPY ./application .
RUN chmod 775 . -R $(ls -I vendor)
RUN composer install
