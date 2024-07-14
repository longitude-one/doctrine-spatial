# doctrine-Spatial
Doctrine-spatial is a doctrine extension. It implements spatial types and functions. 
*As exemple, this extension can help you to know if your favorite superheroes is inside Gotham city.*

Currently, MySQL and PostgreSQL with PostGIS are supported. 
Could potentially add support for other platforms if an interest is expressed.

## Current status
![longitude-one/doctrine--spatial](https://img.shields.io/badge/longitude--one-doctrine--spatial-blue)
![Stable release](https://img.shields.io/github/v/release/longitude-one/doctrine-spatial)
![Minimum PHP Version](https://img.shields.io/packagist/php-v/longitude-one/wkt-parser.svg?maxAge=3600)
[![Packagist License](https://img.shields.io/packagist/l/longitude-one/doctrine-spatial)](https://github.com/longitude-one/doctrine-spatial/blob/main/LICENSE)

[![Last integration test](https://github.com/longitude-one/doctrine-spatial/actions/workflows/full.yaml/badge.svg)](https://github.com/longitude-one/doctrine-spatial/actions/workflows/full.yaml)
[![Maintainability](https://api.codeclimate.com/v1/badges/92b245a85ab4fbaca5d2/maintainability)](https://codeclimate.com/github/longitude-one/doctrine-spatial/maintainability)
[![Downloads](https://img.shields.io/packagist/dm/longitude-one/doctrine-spatial.svg)](https://packagist.org/packages/longitude-one/doctrine-spatial)
[![Test Coverage](https://api.codeclimate.com/v1/badges/92b245a85ab4fbaca5d2/test_coverage)](https://codeclimate.com/github/longitude-one/doctrine-spatial/test_coverage)
[![Documentation Status](https://readthedocs.org/projects/lo-doctrine-spatial/badge/?version=main)](https://lo-doctrine-spatial.readthedocs.io/en/main/?badge=main)

Documentation 
-------------

The [documentation](https://doctrine-spatial.readthedocs.io) explain how to:

* install this doctrine extension,
* configure this extension,
* create spatial entities,
* use spatial functions in your repositories,
* contribute (and test)

The documentation contains a glossary of all available types and all available spatial functions.

## Project origins
This useful library was created by Derek J. Lambert. 
Alexandre Tranchant forked it from [creof/doctrine-spatial](https://github.com/creof/doctrine-spatial)
because project seems to be non-active since 2017.

The longitude-one/doctrine-spatial repository employs a well-structured branching strategy:

* main: Stable 5.0 releases, bug fixes and minor new features (no backward incompatibilities).
* 6.0.x-dev: Major new features (**potential** backward incompatibilities).
* 5.1.x-dev: Controlled deprecations for a smooth 5.1 to 6.0 transition (no backward incompatibilities).

This approach ensures clarity, stability, and maintainability for the project.

Compatibility
-------------
### PHP and Doctrine ORM
This Doctrine extension is compatible with PHP 8.1+ and Doctrine ORM versions `^2.9`, `^3.1`, and aims for continued compatibility with the upcoming major version `^4.x-dev`.
Security fixes will follow the [PHP Roadmap](https://www.php.net/supported-versions.php).

### MySQL 5.7 and 8.0
MySQL5.7 is supported, but is deprecated.
MySQL8.0 is supported.

Known Limitation: `longitude-one/doctrine-spatial` CANNOT store SRID on MySQL. Internally, this extension uses Well Known Text to convert internal types to database type. As `doctrine/orm` does not allow custom persister nor collection persister, we cannot provide two parameters (WKT and SRID). Extented Well Known Text (EWKT) is used to convert internal types to databases but EWKT is only supported by Postgis. I'm trying a new solution: create an external Well Known Bytes converter to be able to use it `convertToDatabaseValue` methods of `doctrine/orm` and `longitude-one/doctrine-spatial`.

### PostgreSQL
This spatial library is compatible with PostgreSql. 
This library is tested with the last versions of Postgis and PostgreSql.

## Help wanted

**Microsoft SQL Server**
I'm searching help to create a docker delivering a Microsoft SQL Server service. So I'll be able to implement
compatibility with this database server.

**MariaDB**
I'm searching help to create a Github action delivering a MariaDB service, to launch test and determine if
this library is compatible.
