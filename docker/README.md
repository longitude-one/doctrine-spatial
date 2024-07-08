Docker
======

This directory is only used to help the contributing developers. It creates a docker environment with PHP 8.1, 
PostgreSQL/PostGis, MySQL 5.7 and 8.0 and Microsoft SQL Server. Feel free to use these files or to use them for 
another solution.

How to start services?
----------------------
```bash
cd docker
docker-compose up
docker exec spatial-php8 composer update
```

How to start test
-----------------
```bash
docker exec spatial-php8 cp docker/phpunit*.xml . 
docker exec spatial-php8 composer test-mysql5
docker exec spatial-php8 composer test-mysql8
docker exec spatial-php8 composer test-pgsql
docker exec spatial-php8 composer test-mssql2017
docker exec spatial-php8 composer test-mssql2019
docker exec spatial-php8 composer test-mssql2022
```

How to start quality tests
--------------------------

Read the [README.md](../quality/README.md) file.