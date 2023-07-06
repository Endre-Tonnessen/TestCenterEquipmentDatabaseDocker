FROM testcenterlaerdal/database
# TestCenterEquipmentDatabase
# fhsinchy/php-nginx-base:php8.1.3-fpm-nginx1.20.2-alpine3.15

# set composer related environment variables
ENV PATH="/composer/vendor/bin:$PATH" \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_VENDOR_DIR=/var/www/vendor \
    COMPOSER_HOME=/composer

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --ansi --version --no-interaction

# install application dependencies
WORKDIR /var/www/app
COPY ./src/composer.json ./src/composer.lock* ./
RUN composer install --no-scripts --no-autoloader --ansi --no-interaction

# add custom php-fpm pool settings, these get written at entrypoint startup
ENV FPM_PM_MAX_CHILDREN=20 \
    FPM_PM_START_SERVERS=2 \
    FPM_PM_MIN_SPARE_SERVERS=1 \
    FPM_PM_MAX_SPARE_SERVERS=3

# set application environment variables
ENV APP_NAME="TestCenterEquipmentDatabase" \
    APP_ENV=production \
    APP_DEBUG=false \ 
    CYPRESS_CACHE_FOLDER="/cypress/.cache" 

# copy entrypoint files and convert line endings to unix
COPY ./docker/docker-php-* /usr/local/bin/
RUN dos2unix /usr/local/bin/docker-php-entrypoint
RUN dos2unix /usr/local/bin/docker-php-entrypoint-dev
RUN chmod a+x /usr/local/bin/docker-php-entrypoint

# copy nginx configuration
COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/default.conf /etc/nginx/conf.d/default.conf

# copy application code
WORKDIR /var/www/app
COPY ./src .
# Copy env
# COPY ./src/.env.example /.env  

RUN composer require laravel/ui:^3.0

RUN composer dump-autoload --no-scripts
# RUN php artisan config:clear
RUN composer run post-autoload-dump --verbose

# RUN composer require laravel/ui
RUN composer install

RUN composer dump-autoload -o \
    && chown -R :www-data /var/www/app \
    && chmod -R 775 /var/www/app/storage /var/www/app/bootstrap/cache

EXPOSE 80

# RUN chown -R www-data:www-data /var/www
# This is bad practice for prod server.
RUN chown -R 777 /var/www 
# RUN chown -R $USER:www-data storage
# RUN chown -R $USER:www-data bootstrap/cache
# RUN chown -R 775 storage
# RUN chown -R 775 bootstrap/cache
# Laravel Artisan commands
# RUN php artisan key:generate
# RUN php artisan storage:link
# RUN php artisan migrate --force


# RUN mkdir -p $CYPRESS_CACHE_FOLDER
RUN npm cache clean --force
RUN npm install cypress -g  --unsafe-perm=true --allow-root cypress
RUN npm install --production

RUN chown -R $USER:www-data storage
RUN chown -R $USER:www-data bootstrap/cache
RUN npm config set cache /tmp --global

# RUN chown -R $USER:$USER /var/www
# RUN chmod -R 755 /var/www
# RUN chmod -R 755 storage/*
# RUN chmod -R 755 bootstrap/*

RUN chown -R www-data:www-data .

# Run mix
#RUN npm run prod
#
## Run Prod caching
#RUN php artisan cache:clear
## Clear and cache config
#RUN php artisan config:cache
## Clear and cache views
#RUN php artisan view:cache

# run supervisor
# CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
