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
[![Documentation Status](https://readthedocs.org/projects/lo-doctrine-spatial/badge/?version=latest)](https://lo-doctrine-spatial.readthedocs.io/en/latest/?badge=latest)

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

The `dev` branch can be used, but be careful backward compatibility aren't warrantied.
The `main` branch is dedicated to fix issue in the last stable version.

Compatibility
-------------
### PHP
This doctrine extension is compatible with PHP 8.1+
Security fixes will follow the [PHP Roadmap](https://www.php.net/supported-versions.php).

### MySQL 5.7 and 8.0
MySQL5.7 is supported, but is deprecated.
MySQL8.0 is supported.

### PostgreSQL
This spatial library is compatible with PostgreSql. 
This library is tested with the last versions of Postgis and PostgreSql.

## Help wanted

**RFC**
I don't have enough money to buy the last edition of the [ISO/IEC 13249-3:2016](https://www.iso.org/standard/60343.html).
I only bought the previous version. This document is essential to know which functions are in Standard and which one
are specific to a database engine. If anyone has bought one and don't use it anymore, feel free to forward it. You can
contact me by mail, specified in `composer.json`.

**Microsoft SQL Server**
I'm searching help to create a docker delivering a Microsoft SQL Server service. So I'll be able to implement
compatibility with this database server.

**MariaDB**
I'm searching help to create a Github action delivering a MariaDB service, to launch test and determine if
this library is compatible.
