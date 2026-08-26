Docker
======

This directory is only used to help contributing developers. It creates a Docker environment with PHP 8.4,
PostgreSQL/PostGIS, MariaDB, SQL Server, and MySQL 8.4. Feel free to use it or another solution.

How to start services?
----------------------

```bash
cd docker
docker-compose up
docker exec spatial-php8 composer update
```

Composer scripts
----------------

Copy the PHPUnit configuration files before running a test script:

```bash
docker exec spatial-php8 cp docker/phpunit*.xml .
```

Run each Composer script inside the PHP container:

```bash
# Run PHP-CS-Fixer, PHPStan, PHPMD, and PHP_CodeSniffer. This command can modify files.
docker exec spatial-php8 composer quality

# Run tests that do not require a database server.
docker exec spatial-php8 composer test-php-only

# Run all database test suites: MariaDB, MySQL, PostgreSQL, and SQL Server.
docker exec spatial-php8 composer test

# Run all database test suites and merge their coverage data into a Clover report.
docker exec spatial-php8 composer test-coverage

# Run only the MariaDB test suite.
docker exec spatial-php8 composer test-mariadb

# Run only the MySQL test suite.
docker exec spatial-php8 composer test-mysql

# Run only the PostgreSQL/PostGIS test suite.
docker exec spatial-php8 composer test-pgsql

# Run only the SQL Server test suite.
docker exec spatial-php8 composer test-sqlserver
```
