FROM drupal:11-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    curl \
 && docker-php-ext-install pdo_pgsql pgsql \
 && rm -rf /var/lib/apt/lists/*

RUN pecl install redis \
 && docker-php-ext-enable redis

RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /opt/drupal
