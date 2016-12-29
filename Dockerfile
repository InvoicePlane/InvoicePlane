FROM php:5.6-apache

# Install and enable PHP extensions
# https://wiki.invoiceplane.com/en/1.0/getting-started/requirements
RUN \
  apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng12-dev \
    librecode-dev \
    libxml2-dev \
  && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install -j$(nproc) gd mcrypt mysqli recode xmlrpc

# Copy InvoicePlane into public directory
COPY . /var/www/html

# Enable .htaccess, set permissions, and enable Apache mod_rewrite
RUN mv /var/www/html/htaccess /var/www/html/.htaccess \
  && chown -R www-data:www-data /var/www/html \
  && a2enmod rewrite
