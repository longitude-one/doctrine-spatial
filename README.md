# Doctrine Spatial

Doctrine Spatial is a Doctrine extension that implements spatial types and functions.

*For example, this extension can help you determine whether your favorite superhero is currently inside Gotham City.*

Currently, MySQL and PostgreSQL (with PostGIS) are supported. Support for other platforms could be added if there is enough interest.

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

## Documentation

The [documentation](https://doctrine-spatial.readthedocs.io) explains how to:

* install this Doctrine extension,
* configure the extension,
* create spatial entities,
* use spatial functions in your repositories,
* contribute to the project (and run the tests).

It also includes a glossary covering all available types and spatial functions.

## Project origins

This library was originally created by Derek J. Lambert.
Alexandre Tranchant forked it from [creof/doctrine-spatial](https://github.com/creof/doctrine2-spatial), as the original project appeared to be inactive since 2017.

The `longitude-one/doctrine-spatial` repository follows a well-structured branching strategy:

* **main** — Stable 5.0 releases: bug fixes and security updates (no breaking changes).
* **5.1.x-dev** — Controlled deprecations to ease the transition from 5.1 to 6.0 (no breaking changes).
* **6.0.x-dev** — Major new features (**may include breaking changes**).

This approach ensures clarity, stability, and maintainability for the project.

## Compatibility

Version 5.0.x has entered maintenance mode and will only receive security patches going forward.

Development efforts are now focused on version 5.1.x, which will introduce the new factories along with deprecation notices for constructors.

Starting with version 6.x, compatibility will only be guaranteed with PHP 8.5 and above. Support for Doctrine 2.9 and MySQL 5.7 will be discontinued.

### PHP and Doctrine ORM

This Doctrine extension is compatible with PHP 8.1+ and Doctrine ORM `^2.9`, `^3.1`, and aims to remain compatible with the upcoming major version `^4.x-dev`.
Security fixes follow the [PHP support roadmap](https://www.php.net/supported-versions.php).

### MySQL 5.7 and 8.0

* MySQL 5.7 is supported but deprecated.
* MySQL 8.0 is fully supported.

**Known limitation:** `longitude-one/doctrine-spatial` cannot store the SRID on MySQL. Internally, the extension uses Well-Known Text (WKT) to convert internal types to database types. Since `doctrine/orm` does not support a custom persister or collection persister, we cannot pass both parameters (WKT and SRID) at once. Extended Well-Known Text (EWKT) would solve this, but it is only supported by PostGIS. A possible solution under investigation is to build an external Well-Known Binary (WKB) converter, usable from the `convertToDatabaseValue` methods of both `doctrine/orm` and `longitude-one/doctrine-spatial`.

### PostgreSQL

This library is compatible with PostgreSQL and is tested against the latest versions of PostGIS and PostgreSQL.

### MariaDB

This library is compatible with MariaDB 10.6.

### MariaDB
This spatial library is compatible with MariaDB 10.6.

## Help wanted

**Microsoft SQL Server**

We're looking for help setting up a Docker image providing a Microsoft SQL Server service, in order to implement compatibility with this database.
