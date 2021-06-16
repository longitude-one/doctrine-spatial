# doctrine-Spatial
Doctrine2 multi-platform support for spatial types and functions. 
Currently MySQL and PostgreSQL with PostGIS are supported. 
Could potentially add support for other platforms if an interest is expressed.

## Current status
[![Build Status](https://travis-ci.org/longitude-one/doctrine-spatial.svg?branch=master)](https://travis-ci.org/longitude-one/doctrine-spatial)
[![Code Climate](https://codeclimate.com/github/longitude-one/doctrine-spatial/badges/gpa.svg)](https://codeclimate.com/github/longitude-one/doctrine-spatial)
[![Coverage Status](https://coveralls.io/repos/longitude-one/doctrine-spatial/badge.svg?branch=master&service=github)](https://coveralls.io/github/longitude-one/doctrine-spatial?branch=master)
[![Downloads](https://img.shields.io/packagist/dm/longitude-one/doctrine-spatial.svg)](https://packagist.org/packages/longitude-one/doctrine-spatial)
[![Documentation Status](https://readthedocs.org/projects/doctrine-spatial/badge/?version=latest)](https://doctrine-spatial.readthedocs.io/en/latest/?badge=latest)

Documentation 
-------------

The [new documentation](https://doctrine-spatial.readthedocs.io) explain how to:

* install this doctrine extension,
* configure this extension,
* create spatial entities,
* use spatial functions into your repositories,
* contribute (and test)

The documentation contains a glossary of all available types and all available spatial functions.

## Project origins
This useful project was created by Derek J. Lambert. 
Alexandre Tranchant forked it from [creof/doctrine-spatial](https://github.com/creof/doctrine-spatial)
because project seems to be non-active since 2017.

The `dev` branch can be used, but be careful backward compatibility aren't warrantied.
The `main` branch will be dedicated to fix issue.
The first release 1.0 will be published during summer 2021.
The second release 2.0 will be published during winter 2022 and compatibility with PHP7.4 will be abandoned because of 
[PHP roadmap](https://www.php.net/supported-versions.php)

Compatibility
-------------
### PHP
This doctrine extension is compatible with PHP 7.4+ and PHP 8.0
Security fixes will follow the [PHP Roadmap](https://www.php.net/supported-versions.php).

### MySQL 5.7 and 8.0
A lot of functions change their names between these two versions. The [MySQL 5.7 deprecated functions](https://stackoverflow.com/questions/60377271/why-some-spatial-functions-does-not-exists-on-my-mysql-server)
are not implemented.

### MariaDB 10
This version is **NOT** compatible with MariaDB version. Some spatial functions seems to work, but their results are 
different from MySQL version (StContains function is a good example). 

### PostgreSQL
This spatial library is compatible with PostgreSql9.6, PostgreSql10 and 
PostgreSql11. I tested it with PostgreSql12. But I do not know how to install a PostgreSql 12 and 13 server on travis to
be sure that library stay compatible. Be careful, this library is only tested with Postgis 2.5+. It is not tested with 
Postgis3.0, but feel free to contribute by updating the [travis configuration](./.travis.yml)
