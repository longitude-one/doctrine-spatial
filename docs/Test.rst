Test environment
================

Doctrine Spatial is tested against MySQL, MariaDB, PostgreSQL/PostGIS, and SQL Server. The local development environment uses Docker Compose.

Prepare the environment
-----------------------

1. `Install Docker <https://docs.docker.com/engine/install/>`_.
2. From the project root, set ``APP_FOLDER`` to the absolute path of the checkout and start the services:

.. code-block:: bash

    export APP_FOLDER="$(pwd)"
    cd docker
    docker-compose up -d
    cd ..

The Compose stack starts six services:

1. PostgreSQL/PostGIS, available at ``postgresql://main:main@127.0.0.1:5432/main``;
2. MySQL 8.4, available at ``mysql://main:main@127.0.0.1:3306/main``;
3. MariaDB (the current ``latest`` image), available at ``mysql://main:main@127.0.0.1:3310/main``;
4. SQL Server 2017, available at ``127.0.0.1:1433``;
5. PHP 8.4, available through the ``spatial-php8`` container;
6. the Sphinx documentation server, available at ``http://127.0.0.1:8800``.

Install dependencies and prepare PHPUnit
----------------------------------------

Composer is available in the PHP container. Install the project dependencies and copy the Docker PHPUnit configurations to the project root:

.. code-block:: bash

    docker exec spatial-php8 composer install
    cp docker/phpunit.*.xml .

The copied configurations use Docker service host names and must be run from the PHP container.

Run tests
---------

Run an individual database suite, the PHP-only suite, or the complete test suite:

.. code-block:: bash

    docker exec spatial-php8 composer test-mariadb
    docker exec spatial-php8 composer test-mysql
    docker exec spatial-php8 composer test-pgsql
    docker exec spatial-php8 composer test-sqlserver
    docker exec spatial-php8 composer test-php-only
    docker exec spatial-php8 composer test

To generate and merge coverage reports, run:

.. code-block:: bash

    docker exec spatial-php8 composer test-coverage

Check code quality
------------------

Before committing, run the configured quality checks:

.. code-block:: bash

    docker exec spatial-php8 composer quality

This command runs PHP-CS-Fixer, PHPStan, PHPMD, and PHP_CodeSniffer. PHP-CS-Fixer may modify files.
