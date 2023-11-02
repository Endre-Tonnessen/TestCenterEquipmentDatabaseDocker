FROM php:8.1.3-fpm-alpine3.15

ENV NGINX_VERSION 1.20.2
ENV NJS_VERSION   0.7.0
ENV PKG_RELEASE   1
ENV PHP_MEMORY_LIMIT=1024MB

# install necessary alpine packages
RUN apk update && apk add --no-cache \
    zip \
    unzip \
    dos2unix \
    supervisor \
    libpng-dev \
    libzip-dev \
    freetype-dev \
    $PHPIZE_DEPS \
    libjpeg-turbo-dev \ 
    npm \ 
    nodejs \ 
    # mysql-client \ 
    mariadb-client

RUN apk add --no-cache mariadb-connector-c-dev

# # Install system dependencies
# RUN apt-get update && apt-get install -y \
#     build-essential \
#     git \
#     curl \
#     libjpeg-dev \
#     libfreetype6-dev \
#     libjpeg62-turbo-dev \
#     libmcrypt-dev \
#     libgd-dev \
#     jpegoptim optipng pngquant gifsicle \
#     libonig-dev \
#     libxml2-dev 


#RUN apt-get install php7.2-mbstring
#RUN apt-get install php7.2-dom

COPY docker/uploads.ini /usr/local/etc/php/conf.d

# compile native PHP packages
RUN docker-php-ext-install \
    gd \
    pcntl \
    bcmath \
    mysqli \
    pdo_mysql \
    pdo \
    && docker-php-ext-enable mysqli

# configure packages
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
# RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
#     --with-gd \
#     --with-jpeg-dir \
#     --with-png-dir \
#     --with-zlib-dir
RUN docker-php-ext-install -j$(nproc) gd

# Change php.ini to allow for larger fileuploads
# RUN sed -E -i -e 's/max_execution_time = 30/max_execution_time = 120/' /usr/local/etc/php/php.ini-production \
#     && sed -E -i -e 's/memory_limit = 128M/memory_limit = 512M/' /usr/local/etc/php/php.ini-production \
#     && sed -E -i -e 's/upload_max_filesize = 2M/upload_max_filesize = 100M/' /usr/local/etc/php/php.ini-production \
#     && sed -E -i -e 's/memory_limit = 128M/memory_limit = 512M/' /usr/local/etc/php/php.ini-development \
#     && sed -E -i -e 's/upload_max_filesize = 2M/upload_max_filesize = 100M/' /usr/local/etc/php/php.ini-development \
#     && sed -E -i -e 's/max_execution_time = 30/max_execution_time = 120/' /usr/local/etc/php/php.ini-development 

# install additional packages from PECL
RUN pecl install zip && docker-php-ext-enable zip 
RUN pecl install igbinary && docker-php-ext-enable igbinary 
RUN pecl install redis && docker-php-ext-enable redis

# install nginx
RUN set -x \
    && nginxPackages=" \
        nginx=${NGINX_VERSION}-r${PKG_RELEASE} \
        nginx-module-xslt=${NGINX_VERSION}-r${PKG_RELEASE} \
        nginx-module-geoip=${NGINX_VERSION}-r${PKG_RELEASE} \
        nginx-module-image-filter=${NGINX_VERSION}-r${PKG_RELEASE} \
        nginx-module-njs=${NGINX_VERSION}.${NJS_VERSION}-r${PKG_RELEASE} \
    " \
    set -x \
    && KEY_SHA512="e7fa8303923d9b95db37a77ad46c68fd4755ff935d0a534d26eba83de193c76166c68bfe7f65471bf8881004ef4aa6df3e34689c305662750c0172fca5d8552a *stdin" \
    && apk add --no-cache --virtual .cert-deps \
        openssl \
    && wget -O /tmp/nginx_signing.rsa.pub https://nginx.org/keys/nginx_signing.rsa.pub \
    && if [ "$(openssl rsa -pubin -in /tmp/nginx_signing.rsa.pub -text -noout | openssl sha512 -r)" = "$KEY_SHA512" ]; then \
        echo "key verification succeeded!"; \
        mv /tmp/nginx_signing.rsa.pub /etc/apk/keys/; \
    else \
        echo "key verification failed!"; \
        exit 1; \
    fi \
    && apk del .cert-deps \
    && apk add -X "https://nginx.org/packages/alpine/v$(egrep -o '^[0-9]+\.[0-9]+' /etc/alpine-release)/main" --no-cache $nginxPackages

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log

# copy supervisor configuration
COPY ./docker/supervisord.conf /etc/supervisord.conf

EXPOSE 80

# run supervisor
# CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
