# Developed by Ikhwan Arief (ikhwan[at]unand.ac.id).
# Copyright (c) 2026 Ikhwan Arief. Copyright held by the application developer.
# LOCAL TEST IMAGE ONLY for NusantaraReborn on OJS 3.4.0-12 (PHP 8.2). Not a production image.
FROM php:8.2-apache
RUN apt-get update \
 && apt-get install -y --no-install-recommends libpng-dev libjpeg-dev libfreetype6-dev libwebp-dev libicu-dev libzip-dev libxml2-dev libxslt1-dev unzip \
 && docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp \
 && docker-php-ext-install -j"$(nproc)" mysqli pdo_mysql gd intl zip bcmath xsl gettext ftp \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*
RUN { \
      echo 'memory_limit=512M'; \
      echo 'upload_max_filesize=64M'; \
      echo 'post_max_size=64M'; \
      echo 'max_execution_time=300'; \
      echo 'display_errors=Off'; \
      echo 'log_errors=On'; \
      echo 'error_reporting=E_ALL'; \
      echo 'error_log=/var/log/apache2/php_errors.log'; \
    } > /usr/local/etc/php/conf.d/ojs.ini
