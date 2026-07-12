# We use the official PHP image as a base image
# We do not use the alpine version because 
# we avoid the Microsoft ODBC driver for SQL Server
FROM php:8.4-fpm-bookworm

# System dependencies and Microsoft repositories
RUN apt-get update && apt-get install -y --no-install-recommends \
        bash git curl gnupg unixodbc-dev \
        libpq-dev libonig-dev default-libmysqlclient-dev \
        libzip-dev unzip \
        autoconf gcc g++ make wget\
    && mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /etc/apt/keyrings/microsoft.gpg \
    && echo "deb [arch=amd64 signed-by=/etc/apt/keyrings/microsoft.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" \
    > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y --no-install-recommends msodbcsql17 \
    && docker-php-ext-install pdo_pgsql pdo_mysql zip \
    && pecl install pcov sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable pcov pdo_pgsql pdo_mysql pdo_sqlsrv sqlsrv \
    && apt-get purge -y autoconf gcc g++ make \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/*

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install symfony
RUN wget https://get.symfony.com/cli/installer -O /tmp/installer && \
    chmod a+x /tmp/installer && \
    /tmp/installer --install-dir=/usr/local/bin/ && \
    rm /tmp/installer

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
