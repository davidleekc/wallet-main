FROM php:8.0-fpm

# Set working directory
WORKDIR /var/www

# Add docker php ext repo
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install php extensions
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions memcached

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    unzip \
    git \
    libsodium-dev \
    libonig-dev \
    libcurl4-gnutls-dev \
    lua-zlib-dev \
    libmemcached-dev \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    nginx \
    exiftool \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install curl mbstring \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install exif \
    && docker-php-ext-install pcntl \
    && docker-php-ext-enable opcache \
    && docker-php-ext-install zip \
    && docker-php-source delete

# Install supervisor
RUN apt-get install -y supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*


# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy code to /var/www
COPY --chown=www:www-data . /var/www
COPY ./composer.lock ./composer.json /var/www/

# add root to www group
RUN chmod -R ug+w /var/www/storage
RUN chmod -R ug+rwx /var/www/storage/logs/

# Copy nginx/php/supervisor configs
RUN cp ./docker/config/supervisor.conf /etc/supervisord.conf
RUN cp ./docker/config/php.ini /usr/local/etc/php/conf.d/app.ini
RUN cp ./docker/config/nginx.conf /etc/nginx/sites-enabled/default

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN chown -R www:www /var/www/artisan && \
    chmod -R ugo+rx /var/www/artisan


# PHP Error Log Files
RUN mkdir /var/log/php
RUN touch /var/log/php/errors.log && chmod 777 /var/log/php/errors.log

RUN chmod -R +x /var/www/bootstrap/cache/
RUN chmod -R +x /var/www/storage/

RUN composer install --optimize-autoloader --no-dev --working-dir="/var/www"
RUN /var/www/artisan key:generate
RUN /var/www/artisan storage:link
RUN chmod +x /var/www/docker/config/run.sh

USER www

EXPOSE 8080
ENTRYPOINT ["/var/www/docker/config/run.sh"]
