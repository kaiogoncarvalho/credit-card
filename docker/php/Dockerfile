FROM php:7.4-fpm

WORKDIR /opt/creditcard

# Install required packages
RUN apt-get update && apt-get install -y \
    git \
    wget \
    zip \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev

# Install PHP extesions
RUN docker-php-ext-install pdo_mysql

# Install Docker X-Debug tools
RUN pecl install xdebug

RUN pecl install mongodb \
	&& 	touch /usr/local/etc/php/conf.d/mongodb.ini \
	&&  echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini \
	&&  rm -rf /tmp/pear

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"\
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

COPY ./files/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
