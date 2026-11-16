FROM php:8.3-apache

# Dépendances système
RUN apt-get update && apt-get install -y \
        libzip-dev \
        zip \
        unzip \
        git \
        libssl-dev \
        pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP : PDO MySQL + MongoDB
RUN docker-php-ext-install pdo pdo_mysql \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Apache : docroot vers /public + mod_rewrite (front controller)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite headers

# Permettre les .htaccess
RUN printf '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
    > /etc/apache2/conf-available/setly.conf \
    && a2enconf setly

WORKDIR /var/www/html
