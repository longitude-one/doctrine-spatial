FROM php:8.1-fpm-alpine

#Install the package(s)
#for Microsoft ODBC driver for SQL Server see https://docs.microsoft.com/en-us/sql/connect/odbc/linux-mac/installing-the-microsoft-odbc-driver-for-sql-server?view=sql-server-linux-ver15#alpine-linux
RUN apk --update --no-cache add bash git postgresql-dev mysql-dev unixodbc-dev autoconf gcc make g++ &&\
        cd /tmp && \
        curl -O https://download.microsoft.com/download/3/5/5/355d7943-a338-41a7-858d-53b259ea33f5/msodbcsql18_18.3.2.1-1_amd64.apk && \
        curl -O https://download.microsoft.com/download/3/5/5/355d7943-a338-41a7-858d-53b259ea33f5/mssql-tools18_18.3.1.1-1_amd64.apk && \
        apk add --allow-untrusted msodbcsql18_18.3.2.1-1_amd64.apk && \
        apk add --allow-untrusted mssql-tools18_18.3.1.1-1_amd64.apk && \
        docker-php-ext-install pdo_pgsql pdo_mysql && \
        pecl install pcov pdo_sqlsrv-5.11.1 sqlsrv-5.11.1  && \
        docker-php-ext-enable pcov pdo_pgsql pdo_mysql pdo_sqlsrv sqlsrv

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
