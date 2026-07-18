# Doctrine Spatial

Doctrine Spatial is a Doctrine ORM/DBAL extension for working with spatial data in PHP applications.

It provides support for spatial types and functions across multiple database engines, while keeping the integration close to Doctrine’s native entity and query model.

## Why use it?

- Map geometry and geography data with Doctrine entities.
- Use spatial predicates and functions directly in DQL.
- Support PostgreSQL/PostGIS, MySQL, MariaDB, and SQL Server.
- Keep database-specific behavior explicit and well documented.

## Current status

![Project repository badge for longitude-one/doctrine-spatial](https://img.shields.io/badge/longitude--one-doctrine--spatial-blue)
![Badge indicating stable release version available](https://img.shields.io/github/v/release/longitude-one/doctrine-spatial)
![Badge indicating minimum supported PHP version required](https://img.shields.io/packagist/php-v/longitude-one/doctrine-spatial.svg?maxAge=3600)
[![Badge showing project license on Packagist](https://img.shields.io/packagist/l/longitude-one/doctrine-spatial)](https://github.com/longitude-one/doctrine-spatial/blob/main/LICENSE)

![Badge showing GitHub Actions full test workflow status](https://img.shields.io/github/actions/workflow/status/longitude-one/doctrine-spatial/.github%2Fworkflows%2Ffull.yaml?label=Full%20test)
![Badge showing code coverage status from Codecov](https://img.shields.io/codecov/c/github/longitude-one/doctrine-spatial)
[![Badge showing documentation build status on ReadTheDocs](https://readthedocs.org/projects/lo-doctrine-spatial/badge/?version=main)](https://lo-doctrine-spatial.readthedocs.io/en/main/?badge=main)

[![Badge showing last month package downloads on Packagist](https://img.shields.io/packagist/dm/longitude-one/doctrine-spatial.svg)](https://packagist.org/packages/longitude-one/doctrine-spatial)
![Badge showing project star count on Packagist](https://img.shields.io/packagist/stars/longitude-one/doctrine-spatial)

## Documentation

The [documentation](https://doctrine-spatial.readthedocs.io) covers installation, configuration, entity mapping, spatial queries, and contribution guidelines.

It also includes a glossary for the main spatial types and functions supported by the library.

## Compatibility

### PHP and Doctrine ORM

| doctrine-spatial | PHP  | Doctrine ORM.      | Status                     | 
|------------------|------|--------------------|----------------------------|
| **5.0**          | 8.1+ | `^2.9`, `^3.1`     | Stable (security fixes).   |
| **5.1**          | 8.2+ | `^2.19`, `^3.1`    | Next version (development) |
| **5.2**          | 8.3+ | `^2.19`, `^3.1`    | Slated for Jan 1, 2027.    |
| **6.0**          | 8.5+ | `^3.6`, `^4.x-dev` | In development.            |

Security fixes follow the [PHP support roadmap](https://www.php.net/supported-versions.php).

### Database compatibility

The versions below reflect the database stack used for the test matrix.

| doctrine-spatial | MySQL.   | MariaDB | PostgreSQL | PostGIS | SQL Server | Status                     |
|------------------|----------|---------|------------|---------|------------|----------------------------|
| **5.0**          | 5.7, 8.0 | 10.6    | 18         | 3.6     | ❌          | Stable (security fixes).   |
| **5.1**          | 8.4      | 10.11   | 18         | 3.6     | 2017       | Next version (development) |
| **5.2**          | 8.4      | 10.11   | 18         | 3.6     | 2017       | Next version (development) |
| **6.0**          | 8.4      | 10.11   | 18         | 3.6     | 2017       | in development.            |

## Known limitations

`longitude-one/doctrine-spatial` cannot currently store the SRID on MySQL or SQL Server. Internally, the extension uses Well-Known Text (WKT) to bridge database values, but this does not allow both WKT and SRID to be passed together through Doctrine’s persistence model. Extended Well-Known Text (EWKT) solves this for PostGIS, but it is not supported by other database engines.

This limitation can be worked around in practice by using the spatial functions `ST_SetSRID` and `ST_SRID` when needed, so the SRID can still be handled at the query level even if it is not persisted directly with the value.

## Project origins

This library was originally created by Derek J. Lambert. Alexandre Tranchant later forked it from [creof/doctrine-spatial](https://github.com/creof/doctrine2-spatial) after the original project appeared to be inactive since 2017.

## Branch strategy

The repository follows a structured release model:

- **main** — Stable 5.0 releases with bug fixes and security updates.
- **5.1.x-dev** — Controlled deprecations to ease the transition toward 6.0.
- **5.2.x-dev** — This branch will drop compatibility with PHP 8.2 and the dedicated code path for it.
- **6.0.x-dev** — Major changes and new features, potentially including breaking changes.

## Contributing

Contributions, bug reports, and documentation improvements are welcome. Please refer to the documentation for the development workflow and test commands.
