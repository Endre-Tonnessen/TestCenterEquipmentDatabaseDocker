FROM php:fpm

COPY ./* /var/www/html

RUN apt-get update
RUN apt-get install -y libmcrypt-dev
RUN apt-get install -y default-mysql-client
RUN apt-get -y install libzip-dev
RUN apt-get -y install zlib1g-dev

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN apt-get install -y \
        libzip-dev \
        zip \
  && docker-php-ext-install zip

RUN docker-php-ext-install zip pdo_mysql gd fileinfo
# zlib1g-dev libpng-dev
#RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.33.11/install.sh | bash
#RUN php -r "readfile('http://getcomposer.org/installer');" | php — — install-dir=/usr/bin/ — filename=composer
#RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
#RUN php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
#RUN php composer-setup.php
#RUN php -r "unlink('composer-setup.php');"

#COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
#RUN curl -sS https://getcomposer.org/installer​ | php -- \
#   --2.2 \
#   --install-dir=/usr/local/binx
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get install -y git
RUN apt-get install -y nodejs
RUN apt-get update && apt-get install -y zlib1g-dev
#  RUN docker-php-ext-install zip


#FROM composer:latest
#RUN composer update
#
#FROM php:7.1-fpm
#RUN artisan key:generate
#
COPY composer.json /var/www/html
COPY composer.lock /var/www/html
RUN composer install --no-scripts --no-autoloader --ansi --no-interaction
# RUN composer update 
# --no-scripts --no-autoloader --ansi --no-interaction

#FROM nodejs:latest
#RUN npm install

RUN apt-get update && apt-get install -y \
    software-properties-common \
    npm
RUN npm install npm@latest -g && \
    npm install n -g && \
    n latest

COPY package.json /var/www/html
COPY package-lock.json /var/www/html
RUN npm install
RUN npm run prod

# Configure project settings
#RUN php artisan key:generate
#RUN php artisan storage:link