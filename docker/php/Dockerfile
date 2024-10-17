FROM php:8.1.18-fpm

COPY php.ini /usr/local/etc/php/

RUN apt update \
    && apt install -y default-mysql-client zlib1g-dev libzip-dev unzip cron \
    && docker-php-ext-install pdo_mysql zip

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && composer self-update

# crontabのコピー（相対パスを修正）
COPY ../crontab /etc/cron.d/cron

# crontabのパーミッションを設定
RUN chmod 0644 /etc/cron.d/cron \
    && crontab /etc/cron.d/cron

# cronとPHP-FPMを同時に起動
CMD service cron start && php-fpm

WORKDIR /var/www