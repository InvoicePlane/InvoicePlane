# PHP version for the development container. InvoicePlane supports 8.2 through 8.5 and CI
# tests all four; this image builds with every extension below on each of them. Override
# with `PHP_VERSION` in docker-compose.yml (or --build-arg) to develop against another.
ARG PHP_VERSION=8.4
FROM php:${PHP_VERSION}-fpm

# Set environment variables
ARG TZ=UTC
ENV TZ ${TZ}

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install dependencies
RUN apt-get update && apt-get install -y \
    mariadb-client \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libkrb5-dev \
    firebird-dev \
    libicu-dev \
    libxml2-dev \
    libxslt1-dev \
    autoconf \
    wget \
    zip \
    unzip \
    cron \
    git \
    libzip-dev \
    locales-all \
    libonig-dev

RUN install-php-extensions \
    bcmath \
    dom \
    exif \
    gd \
    imagick \
    imap \
    intl \
    mysqli \
    pdo_mysql \
    redis \
    soap \
    xdebug \
    xml \
    xsl \
    zip

# Composer. This is a development image and the working tree is bind-mounted, so
# dependencies are managed from inside the container rather than baked into the
# image -- `docker compose exec php composer install` after a fresh clone, and
# again whenever composer.json changes. Same multi-stage copy pattern as
# install-php-extensions above, so no installer script has to be fetched at build
# time.
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Set the timezone
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Comment out xdebug extension line per default
RUN sed -i 's/^zend_extension=/;zend_extension=/g' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Copy xdebug configuration for remote debugging
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

#
#--------------------------------------------------------------------------
# Final Touches
#--------------------------------------------------------------------------
#

# Copy the php-fpm config
COPY ./fpm.conf /usr/local/etc/php-fpm.d/
COPY ./php-dev.ini /usr/local/etc/php/conf.d/php.ini

USER root

# Cleanup all downloaded packages
RUN apt-get -y autoclean && \
    apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/* && \
    rm -f /var/log/lastlog /var/log/faillog

# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

# Set the proper permissions
RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

WORKDIR /var/www/projects/invoiceplane

COPY ./dev-entrypoint.sh /usr/local/bin/dev-entrypoint.sh
RUN chmod +x /usr/local/bin/dev-entrypoint.sh

ENTRYPOINT ["dev-entrypoint.sh"]
CMD ["php-fpm"]
