# Doctrine Spatial

Doctrine Spatial is a Doctrine ORM and DBAL extension for working with spatial data in PHP applications.

It provides support for spatial types and functions across multiple database engines, while keeping the integration close to Doctrine’s native entity and query model.

## Why use it?

- Map geometry and geography data with Doctrine entities.
- Use spatial predicates and functions directly in DQL.
- Work with PostgreSQL/PostGIS, MySQL, MariaDB, and SQL Server.
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

[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/N4X125LWHT)

## Documentation

The [documentation](https://doctrine-spatial.readthedocs.io) covers installation, configuration, entity mapping, spatial queries, and contribution guidelines.

It also includes a glossary for the main spatial types and functions supported by the library.

## Roadmap

> [!NOTE]
> A major release may increase the minimum supported PHP version. Other breaking changes are documented in the changelog.

| Version | PHP compatibility           | Tested on       | Doctrine ORM             | Tested against Doctrine ORM | Released     | Active support   | Security fixes   |
|---------|-----------------------------|-----------------|--------------------------|-----------------------------|--------------|------------------|------------------|
| 5       | 8.1 - 8.2 - 8.3 - 8.4 - 8.5 | From 8.1 to 8.3 | ^2.1 - ^3.0 - ^4.0.x-dev | ^2.1 - ^3.0 - ^4.0.x-dev    | 04 May 2024  | 31 August 2026   | 31 December 2026 |
| 6       | 8.4 - 8.5                   | 8.4 - 8.5       | ^3.6 - ^4.0.x-dev        | ^3.6 - 4.0.x-dev            | August 2026  | 31 December 2027 | 31 December 2028 |
| 7       | 8.4 - 8.5                   | 8.4 - 8.5       | ^3.6 - ^4.0.x-dev        | ^3.6 - 4.0.x-dev            | Unknown      | Unknown          | Unknown          |
| 8       | 8.5                         | 8.5             |                          | ^3.6 - 4.0.x-dev.           | January 2028 | 31 December 2028 | 31 December 2029 |

> [!NOTE]
> **Why does PHP support jump between versions 5 and 6?**
> PHP 8.4 introduces lazy objects. Designed especially for Doctrine-created objects, this feature removes the need for proxies and dedicated proxy namespaces.
> This is a major change that improves performance. Maintaining a library that supports both approaches complicates development and maintenance.
> Version 5 of Doctrine Spatial provides a smooth transition: it is compatible with PHP 8.1 through 8.5 and with Doctrine ORM versions 2, 3, and even 4.
> You are encouraged to upgrade to PHP 8.4 and Doctrine ORM 3.6, which introduces lazy objects, before upgrading to Doctrine Spatial version 6.
> Version 6 contains no breaking changes other than higher minimum supported versions of PHP and Doctrine ORM.

Version 7 is planned as an intermediate release between versions 6 and 8, with the following new features:

- Support for Z (elevation) and M (measure) dimensions
- SRID support for MySQL and SQL Server
- A new library ecosystem, including doctrine-spatial-types, geo-parser version 4, and new writers

The release date will depend on the availability of contributors and on my ability to fund subscriptions to AI development tools.
Donations of coffee through [Ko-fi](https://ko-fi.com/longitudeone) will go either towards actual coffee or towards AI subscriptions.

### Release

| Status                      | Doctrine Spatial | PHP  | Doctrine ORM       |
|-----------------------------|------------------|------|--------------------|
| Stable (security fixes)     | **5.0**          | 8.1+ | `^2.9`, `^3.1`     |
| Next release                | **6.0**          | 8.4+ | `^3.6`, `^4.x-dev` |
| Next major version          | **7.0**          | 8.4+ | `^3.6`, `^4.x-dev` |
| Planned for 1 January 2028  | **8.0**          | 8.5+ | `^3.6`, `^4.x-dev` |

### Database testing

The following versions reflect the database stack used in [the minimal database server test matrix](./.github/workflows/tests-minimum-database-versions.yaml).

| Doctrine Spatial | MySQL    | MariaDB | PostgreSQL | PostGIS | SQL Server |
|------------------|----------|---------|------------|---------|------------|
| **5.0**          | 5.7, 8.0 | 10.6    | 18         | 3.6     | ❌          |
| **6.0**          | 8.4      | 10.22   | 16         | 3.5     | 2023       |
| **7.0**          | 8.4      | 10.22   | 16         | 3.5     | 2023       |
| **8.0**          | 8.4      | 10.22   | 16         | 3.5     | 2023       |

When successful, the following versions are tested using [the maximum database server test matrix](./.github/workflows/tests-maximum-database-versions.yaml).

| Doctrine Spatial | MySQL    | MariaDB | PostgreSQL | PostGIS | SQL Server |
|------------------|----------|---------|------------|---------|------------|
| **5.0**          | 5.7, 8.0 | 10.6    | 18         | 3.6     | ❌          |
| **6.0**          | 9.7      | 12.3    | 18         | 3.6     | 2025       |
| **7.0**          | 9.7      | 12.3    | 18         | 3.6     | 2025       |
| **8.0**          | 9.7      | 12.3    | 18         | 3.6     | 2025       |

These versions may change rapidly as the test matrix evolves.

## Known limitations

`longitude-one/doctrine-spatial` cannot currently persist SRIDs in MySQL or SQL Server. Internally, the extension uses Well-Known Text (WKT) to translate values to and from the database, but Doctrine’s persistence layer cannot pass a WKT value and an SRID together. Extended Well-Known Text (EWKT) addresses this limitation in PostGIS, but other database engines do not support it.

You can work around this limitation by using the `ST_SetSRID` and `ST_SRID` spatial functions when needed. This allows SRIDs to be handled at the query level, even though they are not stored directly with the value.

## Branch strategy

The repository follows a structured release model:

- **main** — No breaking changes; only the minimum supported versions of PHP and Doctrine ORM may change.
- **5.0.x-dev** — Security updates for 5.0.x only.
- **6.1.x-dev** — New features without breaking changes.
- **7.0.x-dev** — New features with breaking changes.

## Contributing

Contributions, bug reports, and documentation improvements are welcome. Please refer to the documentation for the development workflow and test commands.
