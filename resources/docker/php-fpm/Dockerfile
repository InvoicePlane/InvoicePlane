FROM php:8.1-fpm

# Set environment variables
ARG TZ=UTC
ENV TZ ${TZ}

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install dependencies
RUN apt-get update && apt-get install -y \
    mariadb-client \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libcurl4-nss-dev \
    libc-client-dev \
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
    libonig-dev \
    wkhtmltopdf

RUN install-php-extensions \
    bcmath \
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
    xsl \
    zip

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
COPY ./php-dev.ini /usr/local/etc/php/conf.d/

USER root

# Cleanup all downloaded packages
RUN apt-get -y autoclean && \
    apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/* && \
    rm /var/log/lastlog /var/log/faillog

# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

# Set the proper permissions
RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

WORKDIR /var/www/projects/invoiceplane

CMD ["php-fpm"]
