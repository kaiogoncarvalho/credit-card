FROM php:7.4-fpm

WORKDIR /opt/creditcard

# Install extensions
RUN apt-get update && apt-get install -y \
        git \
        wget \
        zip \
        libfreetype6-dev \
        libwebp-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libgmp-dev \
        libldap2-dev \
        build-essential

# Install Docker PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install -j$(nproc) gd pdo_mysql

# Install Docker X-Debug tools
RUN pecl install xdebug

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"\
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

COPY ./files/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
