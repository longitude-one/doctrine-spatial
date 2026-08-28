Test environment
================

If you want to contribute to this library, you're welcome. This section will help you to prepare your development
environment.

How to prepare environment?
---------------------------

Doctrine Spatial is tested against MySQL, MariaDB, PostgreSQL/PostGIS, and SQL Server.

1. [Install docker](https://docs.docker.com/engine/install/),
2. Go to the docker directory and start docker

.. code-block:: bash

    cd <project_directory>
    cd docker
    docker-compose up -d
    cd ..

Done! Your environment is ready with six services:

1. A PostgreSQL/PostGIS service, available at ``postgresql://main:main@127.0.0.1:5432/main``.
2. A MySQL 8.4 service, available at ``mysql://main:main@127.0.0.1:3306/main``.
3. A MariaDB service, available at ``mysql://main:main@127.0.0.1:3310/main``.
4. A SQL Server 2017 service, available at ``127.0.0.1:1433``.
5. A PHP 8.4 service; check it with ``docker exec spatial-php8 php -v``.
6. A documentation service, available at ``http://127.0.0.1:8800``.

Composer is installed on spatial-php8.

.. code-block:: bash

    docker exec spatial-php8 composer -v

How to start test?
--------------------------
Copy docker/phpunit.*.xml to the project directory

.. code-block:: bash

    cp docker/phpunit.*.xml .

Then, you can run the test suites in the PHP container:

.. code-block:: bash

    docker exec spatial-php8 composer test-mariadb
    docker exec spatial-php8 composer test-mysql
    docker exec spatial-php8 composer test-pgsql
    docker exec spatial-php8 composer test-sqlserver

After any update, before any commit, simply check your code with this composer command:

.. code-block:: bash

    docker exec spatial-php8 composer quality
