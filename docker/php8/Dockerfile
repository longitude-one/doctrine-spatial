FROM php:8.2-fpm-alpine

RUN apk --update --no-cache add bash git postgresql-dev mysql-dev autoconf gcc make g++ \
        && docker-php-ext-install pdo_pgsql pdo_mysql  \
        && pecl install pcov \
        && docker-php-ext-enable pcov pdo_pgsql pdo_mysql

#Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

##Install symfony
RUN wget https://get.symfony.com/cli/installer -O /tmp/installer && \
    chmod a+x /tmp/installer && \
    /tmp/installer --install-dir=/usr/local/bin/ && \
    rm /tmp/installer

WORKDIR /var/www

CMD php-fpm

EXPOSE 9000
