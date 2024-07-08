Docker
======

This directory is only used to help the contributing developers. It creates a docker environment with PHP 8.1, 
PostgreSQL/PostGis, MySQL5.7 and MySQL8.0. Feel free to use it or to use another solution.

How to start services?
----------------------
```bash
cd docker
docker-compose up
docker exec spatial-php composer update
```

How to start test
-----------------
```bash
docker exec spatial-php8 cp docker/phpunit*.xml . 
docker exec spatial-php8 composer test-mysql5
docker exec spatial-php8 composer test-mysql8
docker exec spatial-php8 composer test-pgsql
