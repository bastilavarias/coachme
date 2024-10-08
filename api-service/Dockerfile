FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    cron \
    default-mysql-client

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

RUN curl -sS https://getcomposer.org/installer | php -- --version=2.4.1 && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/coachme-service

COPY . /var/www/coachme-service
COPY --chown=www-data:www-data . /var/www/coachme-service

RUN chmod -R 777 /var/www/coachme-service/storage \
    && chmod -R 777 /var/www/coachme-service/storage/logs \
    && chmod -R 777 /var/www/coachme-service/bootstrap/cache

RUN composer install

RUN echo "* * * * * www-data /usr/bin/php /var/www/coachme-service/artisan schedule:run >> /var/log/cron.log 2>&1" > /etc/cron.d/my-crontab
RUN chmod 0644 /etc/cron.d/my-crontab

RUN crontab /etc/cron.d/my-crontab

RUN touch /var/log/cron.log

RUN sed -i 's/listen = .*/listen = 0.0.0.0:5000/' /usr/local/etc/php-fpm.d/zz-docker.conf

EXPOSE 5000

CMD ["sh", "-c", "cron && php-fpm"]
