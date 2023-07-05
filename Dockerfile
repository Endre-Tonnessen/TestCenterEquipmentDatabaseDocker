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
    APP_DEBUG=false

# copy entrypoint files and convert line endings to unix
COPY ./docker/docker-php-* /usr/local/bin/
RUN dos2unix /usr/local/bin/docker-php-entrypoint
RUN dos2unix /usr/local/bin/docker-php-entrypoint-dev

# copy nginx configuration
COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/default.conf /etc/nginx/conf.d/default.conf

# copy application code
WORKDIR /var/www/app
COPY ./src .

RUN composer dump-autoload --no-scripts
# RUN php artisan config:clear
RUN composer run post-autoload-dump --verbose

# RUN composer require laravel/ui
RUN composer install

RUN composer dump-autoload -o \
    && chown -R :www-data /var/www/app \
    && chmod -R 775 /var/www/app/storage /var/www/app/bootstrap/cache

EXPOSE 80

# Laravel Artisan commands
# RUN php artisan key:generate
# RUN php artisan storage:link
# RUN php artisan migrate --force

# Install node
#RUN apt-get update && apt-get install -y \
#    # software-properties-common \
#    npm
RUN npm install
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
