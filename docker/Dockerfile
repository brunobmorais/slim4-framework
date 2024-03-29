FROM php:8.1-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
  autoconf \
  build-essential \
  apt-utils \
  zlib1g-dev \
  libzip-dev \
  unzip \
  zip \
  libmagick++-dev \
  libmagickwand-dev \
  libpq-dev \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng-dev \
  unixodbc \
  unixodbc-dev \
  freetds-dev \
  freetds-bin \
  tdsodbc

RUN docker-php-ext-configure soap --enable-soap

RUN docker-php-ext-install gd intl pdo_mysql pdo_pgsql mysqli zip pdo_dblib soap

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN docker-php-ext-enable pdo_dblib

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#upload
RUN echo "file_uploads = On\n" \
         "error_reporting = off\n" \
         "shot_opentag = On\n" \
         "memory_limit = 500M\n" \
         "upload_max_filesize = 500M\n" \
         "post_max_size = 2048M\n" \
         "max_execution_time = 600\n" \
         "max_input_vars=5000\n" \
         > /usr/local/etc/php/conf.d/uploads.ini

# Clear package lists
RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Permissions
RUN chown -R root:www-data /var/www/html
RUN chmod u+rwx,g+rx,o+rx /var/www/html
RUN find /var/www/html -type d -exec chmod u+rwx,g+rx,o+rx {} +
RUN find /var/www/html -type f -exec chmod u+rw,g+rw,o+r {} +

RUN chown -R www-data:www-data /var/www/html

WORKDIR /var/www/html

RUN a2enmod rewrite
RUN a2enmod ssl

EXPOSE 80
EXPOSE 443