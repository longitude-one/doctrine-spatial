# Changelog

All notable changes to this project will be documented in this file. See [commit-and-tag-version](https://github.com/absolute-version/commit-and-tag-version) for commit guidelines.

## [6.0.0-RC.0](https://github.com/longitude-one/doctrine-spatial/compare/5.0.5...6.0.0-RC.0) (2026-09-02)

### ⚠ BREAKING CHANGES

* MariaDB and MySQL SpBuffer functions have been removed. Please use Common/ScBuffer instead.
* StSrid can no longer be used with MariaDB or PostgreSQL. Please use Standard/StSetSrid to update the SRID, or Common/ScSrid to read it instead.

### ✨ New Features

* Add complete SQL Server 2017+ support to doctrine-spatial ([6c421cb](https://github.com/longitude-one/doctrine-spatial/commit/6c421cbc7a58619c7ddb3b5a643ed529ce317104)), closes [#153](https://github.com/longitude-one/doctrine-spatial/issues/153) [#154](https://github.com/longitude-one/doctrine-spatial/issues/154) [#152](https://github.com/longitude-one/doctrine-spatial/issues/152)
* add deprecation handling for SpBuffer DQL function in MariaDB and MySQL ([ec267ba](https://github.com/longitude-one/doctrine-spatial/commit/ec267ba52024db755f7bbeaf12af8237ee1cf10e))
* add quality assurance scripts for PHP-CS-Fixer, PHPStan, and PHP Mess Detector and PHP code sniffer ([44e4cae](https://github.com/longitude-one/doctrine-spatial/commit/44e4cae5943966e0e5f0d3294db65f163063f019))
* add README for directory structure of functions ([88a77b9](https://github.com/longitude-one/doctrine-spatial/commit/88a77b9caa7702e4b199dad39f1d4edb73098e4a))
* add ScBuffer DQL function and corresponding tests for PostgreSQL, SQLServer, MariaDB, and MySQL servers. ([55810a8](https://github.com/longitude-one/doctrine-spatial/commit/55810a8ac64ffc0be95818d420d8931c5f566ed3))
* add SQL declaration snippet for spatial functions and support for SQL Server platform ([08c4978](https://github.com/longitude-one/doctrine-spatial/commit/08c4978eadf42673db965c6087efad4631ff49ef))
* add SQL Server support for spatial functions St_Contains, ST_Equals, ST_GeomFromText and corresponding tests ([a7c340e](https://github.com/longitude-one/doctrine-spatial/commit/a7c340e3a82fae70300be608ac8678f8a3e738bb))
* add SQL Server support to ST_Centroid function and its tests ([c4a7188](https://github.com/longitude-one/doctrine-spatial/commit/c4a71888fda65dab83a87d1dc81df2e5f7ae6faf))
* add SQL Server support to ST_ConvexHull function and its tests ([7a2dcbb](https://github.com/longitude-one/doctrine-spatial/commit/7a2dcbbd1df375151f1889aba7822e6d9e54b642))
* add SQL Server support to ST_Crosses function and its tests ([7a56f4a](https://github.com/longitude-one/doctrine-spatial/commit/7a56f4a5e19376f68393681136ba8f8edbb98e40))
* add SQL Server support to ST_Disjoint function and its tests ([bfb3397](https://github.com/longitude-one/doctrine-spatial/commit/bfb3397214e5daee233b8d0e72e5031ab81971e4))
* add SQL Server support to StDifference and StIsEmpty functions and their tests ([166e0f3](https://github.com/longitude-one/doctrine-spatial/commit/166e0f395524619d20a3c8767fd1d40764cdfbda))
* add SQL Server support to StDimension function and its tests ([9ce8a2e](https://github.com/longitude-one/doctrine-spatial/commit/9ce8a2eb136a1283b55e24baa2da97472fa2bd35))
* add SQLServerPlatform support to StAsBinary function and its tests ([1a07cda](https://github.com/longitude-one/doctrine-spatial/commit/1a07cda731dfbadae6b618953562766d3a978522))
* add SQLServerPlatform support to StAsText function and its tests ([1aa3a6c](https://github.com/longitude-one/doctrine-spatial/commit/1aa3a6c8d37b6b9ef727680736dc35705aeffaeb))
* add SQLServerPlatform support to StBoundary function and its tests ([11f0eb6](https://github.com/longitude-one/doctrine-spatial/commit/11f0eb62833a4e7ecf70a6dc04bdb42516396368))
* add support for custom driver options in connection parameters ([499f4d5](https://github.com/longitude-one/doctrine-spatial/commit/499f4d567362b18f141f75c7b1f7f5ff834fefac))
* add support for SQL Server 2017 in doctrine spatial extension ([3b8b61e](https://github.com/longitude-one/doctrine-spatial/commit/3b8b61e78b8a7ca8e2439e16d9eb0e2329e3458e))
* implement MatchPlatformHelper for spatial platform resolution in AbstractSpatialDQLFunction ([382ecd1](https://github.com/longitude-one/doctrine-spatial/commit/382ecd1253ab28702a14ca29e54e600ca6da316a))
* Update SpSimplify to support three parameters and add corresponding test ([e74db22](https://github.com/longitude-one/doctrine-spatial/commit/e74db22ef5754ec150d803de9fdd10cdf195695b))

### 🐛 Bug Fixes

* add Microsoft ODBC Driver installation step for SQL Server in GitHub Actions workflow ([744b533](https://github.com/longitude-one/doctrine-spatial/commit/744b5338c1711e1db3b8d54952370c82a59df945))
* add mssql-2017 service configuration GitHub Actions workflow ([ab5d35d](https://github.com/longitude-one/doctrine-spatial/commit/ab5d35dca51f8d54368d2383d75726e7bc450401))
* correct namespace separator in query string for ST_Relates function test ([fea55b9](https://github.com/longitude-one/doctrine-spatial/commit/fea55b9727d051ae0f1326277daa1a7cf02e0e61))
* correct SQL Server function parameter handling in getFunctionSqlDeclaration ([7635daf](https://github.com/longitude-one/doctrine-spatial/commit/7635dafd16eabe558e013ae891494ba796f1ec7c))
* improve error message for unsupported DBAL platforms in AbstractSpatialDQLFunction ([7153db0](https://github.com/longitude-one/doctrine-spatial/commit/7153db06ae92e08639cfbdf901c49b03f350bcbc))
* improve query string formatting and use class constants in GeometryWalkerTest ([d0727e5](https://github.com/longitude-one/doctrine-spatial/commit/d0727e54322ba15c453a7fe5e7f3636369968a53)), closes [#135](https://github.com/longitude-one/doctrine-spatial/issues/135)
* improve README clarity and structure for better understanding ([fff7bd4](https://github.com/longitude-one/doctrine-spatial/commit/fff7bd4cdd4bdd2c7113458cac9a8a412ae1215d))
* refactor Dockerfile: Add missing libzip-dev for PHP extensions to fix composer update ([07b061e](https://github.com/longitude-one/doctrine-spatial/commit/07b061e9a48d81dffef376c902922ca2d5c5c926))
* remove license comment from SqlServer class documentation ([a7fe1fd](https://github.com/longitude-one/doctrine-spatial/commit/a7fe1fd64abc8742ad51ed10fb86f2e29dcaeb6d))
* replace trim with mb_trim for alias handling in GeometryWalker ([133aaac](https://github.com/longitude-one/doctrine-spatial/commit/133aaacc97956ef762175a202a18a5adea7f1c73))
* skip assertion for SRID in SQL Server test until feature is implemented ([093e927](https://github.com/longitude-one/doctrine-spatial/commit/093e9279e222bd23a86b3ce6067ffdd8f620a2d6))
* some miscellaneous typo ([5117d28](https://github.com/longitude-one/doctrine-spatial/commit/5117d2830bf640d3c74f7bdce47ba823f0fa5781))
* update codecov action to use specific clover.xml file ([879f2ef](https://github.com/longitude-one/doctrine-spatial/commit/879f2ef8dde3091e060d4834d8d1fde291c4463a))
* update compatibility information for versions 5.0.x, 5.1.x, and 5.2.x in README ([b713098](https://github.com/longitude-one/doctrine-spatial/commit/b71309825514f7bff6329df7258bce8da5c97d6d))
* update copyright years in LICENSE file ([5efb91e](https://github.com/longitude-one/doctrine-spatial/commit/5efb91efe1dd009055245177c9672980d76a585b))
* update database host in PHPUnit configuration to localhost ([797fb3e](https://github.com/longitude-one/doctrine-spatial/commit/797fb3e2b21869e8c3d92f824422e8e3bef5b2e9))
* update PHP compatibility and release status in README ([f431c99](https://github.com/longitude-one/doctrine-spatial/commit/f431c998472642c3235b41007e57e5d7cd456c6c))
* update PHP version to 8.4 in CI workflow and adjust matrix configurations ([c6bbf53](https://github.com/longitude-one/doctrine-spatial/commit/c6bbf53c25048e6cc641705dc611159ead73885d))
* update ST_Equals queries in StGeomFromTextTest to use parameterized results ([8548259](https://github.com/longitude-one/doctrine-spatial/commit/8548259c8f333fbd30b48f0b0addd6b583d7371d))
* update test to clarify SRID storage limitation in SQL Server ([c9d3ce6](https://github.com/longitude-one/doctrine-spatial/commit/c9d3ce6bb75a7186b49cafac6e113105fe0ba7a9))

### ♻️ Refactoring

* Remove deprecated platforms from StSrid function and its tests ([bc66d0f](https://github.com/longitude-one/doctrine-spatial/commit/bc66d0f6eda6c6a24ccc4a217a45ef540100c981))
* Remove deprecated SpBuffer DQL functions and related tests ([e17acd5](https://github.com/longitude-one/doctrine-spatial/commit/e17acd500fb0a3c40399a35fad951b259dc95d22))
* replace conditional statements with match expression for platform-specific function additions ([79457fd](https://github.com/longitude-one/doctrine-spatial/commit/79457fdc062306207b55b8a1ca7c55077056963f))

### 📚 Documentation

* Refresh setup and Symfony configuration ([6f36bc5](https://github.com/longitude-one/doctrine-spatial/commit/6f36bc52926c4da2e6de952b08657ace848cab22)), references [#141](https://github.com/longitude-one/doctrine-spatial/issues/141)
* update installation and contribution instructions, improve test environment setup, and bump version to 6.0.0 ([b2114b7](https://github.com/longitude-one/doctrine-spatial/commit/b2114b7f0018f2313a81b446bf70e96b975283f1))

### 🌳 Environmental Impact

* **ci:** CI workflows and database configurations ([4f75ecf](https://github.com/longitude-one/doctrine-spatial/commit/4f75ecfc77aabe6f5f4be90a3bf3c90bbe6e1598))
* Useless file from package export removed. ([799709c](https://github.com/longitude-one/doctrine-spatial/commit/799709c314031a6e1ee83da90d1cbe6626640566))

### 👷 CI/CD

* Branches updated ([57e13db](https://github.com/longitude-one/doctrine-spatial/commit/57e13db99c0a321086842094ea09df356e6bd6ee))
* Merge each workflow ([f1cdd28](https://github.com/longitude-one/doctrine-spatial/commit/f1cdd280723854b404c12aad94ff995014b2f633))

### 🔧 Maintenance

* add custom updaters for Installation.rst and conf.py to manage versioning ([4b8fdde](https://github.com/longitude-one/doctrine-spatial/commit/4b8fdde04391200d5d4ca60d7d89a590d1067a1a))
* Add Ko-fi funding link and update FUNDING.yml ([aa44e1f](https://github.com/longitude-one/doctrine-spatial/commit/aa44e1fccd67a12ee26f8fd879dc8a842b812f0f))
* PhpUnit upgraded ([651615a](https://github.com/longitude-one/doctrine-spatial/commit/651615ae3cecb203ef124ed49522753e4c708b6e))
* Update authors and adjust autoloading to PSR-4 standards ([d930b34](https://github.com/longitude-one/doctrine-spatial/commit/d930b34a5e9ed872bf5c8eacf1c24fddcc8ddf96))
* Update dependencies and replace ArrayCachePool with ArrayAdapter ([8c49f7b](https://github.com/longitude-one/doctrine-spatial/commit/8c49f7be42c9ff020535e3ce339572aeeda1767e))
* Update Docker environment and enhance test scripts for PHP 8.4 compatibility ([3e31b07](https://github.com/longitude-one/doctrine-spatial/commit/3e31b07f35a05924886f4d1d043b09b4cb14fbcc))

### 📊​ Quality tools

* Add checks for enableNativeLazyObjects method and update PHP workflow to show dependencies ([94a15f9](https://github.com/longitude-one/doctrine-spatial/commit/94a15f90598a8ac0f9640509040ad5be16997726))
* Add commit-and-tag-version tool ([1a8284e](https://github.com/longitude-one/doctrine-spatial/commit/1a8284e40d2c8465da475a7377c3026382bc6e18))
* Php-CS-Fixer updated ([cc97372](https://github.com/longitude-one/doctrine-spatial/commit/cc97372dd6d7ef8376dd3dc7e87b08d0b09efb6d))
* PHPStan Upgraded and code fixed ([ded7d75](https://github.com/longitude-one/doctrine-spatial/commit/ded7d75d71898384a72f966580a8acc261c1976c))
* Refine CyclomaticComplexity rule configuration in PHPMD ruleset ([bcf77b4](https://github.com/longitude-one/doctrine-spatial/commit/bcf77b45e1806d6be0569db4196da9dfbf4c197b))
* Update PHPCS CyclomaticComplexity rule and enhance test documentation ([c8c2e2e](https://github.com/longitude-one/doctrine-spatial/commit/c8c2e2ec8f42a88774c96018be66413e177fc6cf))
* Update SpSimplifyTest to assert expected result structure ([b1afea3](https://github.com/longitude-one/doctrine-spatial/commit/b1afea38f4dd736aa706366c66976f1e00880b04))

### 📗​ PHPUnit tests

* Add 'mariadb-only' group to PHPUnit XML configurations ([c4e3e7a](https://github.com/longitude-one/doctrine-spatial/commit/c4e3e7a6973a6ac215a05a3bb945553ebc8a42bd))
* test coverage upgraded ([#166](https://github.com/longitude-one/doctrine-spatial/issues/166)) ([a9deb8d](https://github.com/longitude-one/doctrine-spatial/commit/a9deb8de0b2ed1ef62f45ed4a564df732190f62f))

## 5.0.5 (2026-08-23)

### Features

* apply if isset to coalescing ([179c1ff](https://github.com/longitude-one/doctrine-spatial/commit/179c1ffc0cb437c8ebbf2842e5ca940da2af84fe))
* openup versions available without breaks ([#134](https://github.com/longitude-one/doctrine-spatial/issues/134)) ([7931b9a](https://github.com/longitude-one/doctrine-spatial/commit/7931b9a29c72bee75987ffc9b2ce90101cc7283f))

### Bug Fixes

* improve README clarity and structure for better understanding ([bbbeb3f](https://github.com/longitude-one/doctrine-spatial/commit/bbbeb3ff5dd9ec9a80cb4e2dc470d5f6754b0e02))
* update codecov action to use specific clover.xml file ([9415b03](https://github.com/longitude-one/doctrine-spatial/commit/9415b03bce307cbe3aeb97eed567e989c44b81e3))
* update compatibility information for versions 5.0.x, 5.1.x, and 5.2.x in README ([2674e62](https://github.com/longitude-one/doctrine-spatial/commit/2674e6290374d51c836d460b6646408652d17fff))
* update copyright years in LICENSE file ([1b26cb9](https://github.com/longitude-one/doctrine-spatial/commit/1b26cb953333f56459960fe901a8fcda4cbf56be))
* update dg/bypass-finals version constraint to be less than 1.11 ([8c7838e](https://github.com/longitude-one/doctrine-spatial/commit/8c7838e1f8d9c2bb1a93a022535bfd9cce4b4577))
* update GeometryWalker and OrmTestCase for compatibility with Doctrine ORM 4.0 ([018eb4c](https://github.com/longitude-one/doctrine-spatial/commit/018eb4cc091ebd7aed74a842e7676d453d0ea9eb))
* update lazy loading configuration for compatibility with Doctrine ORM <3.5 and PHP 8.4+ ([00f2af8](https://github.com/longitude-one/doctrine-spatial/commit/00f2af806bb340e2aa3710993b94362c2f7493c7))
* update phpunit dependencies in composer.json ([#147](https://github.com/longitude-one/doctrine-spatial/issues/147)) ([c5106a6](https://github.com/longitude-one/doctrine-spatial/commit/c5106a69040ba80466c24aa3d016c1bb94beec25))

## 5.0.4

Version 5.0.4 improved contributor and CI tooling rather than changing the public spatial API. It added explicit container platforms for PostGIS and MySQL, corrected Docker and installation instructions, added repository attributes, removed the redundant `ext-json` requirement, and updated PHP CodeSniffer, PHP CS Fixer, PHP Mess Detector, PHPStan, and their dependencies. The release also incorporated MariaDB support work from the main branch.

## 5.0.3

Version 5.0.3 extended `ST_SRID()` support to accept two parameters. It added a PostgreSQL-specific SRID function, deprecated use of the standard SRID function on PostgreSQL, and included tests for the new behaviour and the emitted deprecations.

## 5.0.2

Version 5.0.2 addressed a false positive that reported the SQL Server platform as unavailable. The surrounding maintenance work also covered an edge case in `isClosed` for empty line strings, documentation adjustments, and quality-tool baseline updates, keeping the patch focused on reliable platform detection and regression coverage.

## 5.0.1

Version 5.0.1 corrected an unexpected conversion of coordinate values while storing points. It also separated geodetic and Cartesian coordinates through new interfaces, introduced an internal `RangeException`, resolved the related issues, and refined CI so that duplicate checks are avoided across pushes and pull requests.

## 5.0.0

Version 5.0.0 focused on compatibility with modern Doctrine ORM versions: it added support for Doctrine ORM 2.19 and 3.1, with testing also performed against the 3.2 and 4.0 development lines. The release upgraded Composer, PHPUnit, parsers, GitHub Actions, and the quality-tool suite; removed deprecated APIs; strengthened the test harness and coverage workflow; and introduced interfaces and return types that prepared the library for later releases.

## 4.0.0

Version 4.0.0 modernized the parsing layer by adopting `longitude-one/wkt-parser` and subsequently pinning it to the 1.0 release. It removed support for PHP 7.4 and PHP 8.0, migrated test fixtures from annotations to attributes, improved cross-platform floating-point tests, and refreshed the CI and code-quality configuration. These changes established the technical baseline for the 4.x series.

## LongitudeOne/doctrine-spatial [4.0.0-dev]

### Changed

- longitude-one/wkt-parser replaces creof/wkt-parser

### Removed

- Removing support of PHP7.4, PHP8.0

### TODO

- Support of CircleCI on Github actions (help is welcomed)
- Support for code coverage on Github Actions (help is welcomed)

## LongitudeOne/doctrine-spatial [3.0.2] - 2022-02-16

### Added

- longitude-one/doctrine-spatial replaces CrEOF/doctrine2-spatial
- Support of PHP8.0
- Support for Postgis2.1, PostGis3.0, PostGis3.1
- Namespaces have been updated from CrEOF/Spatial to LongitudeOne/Spatial
- Github actions added for our internal test

### Removed

- Removing support of PHP7.2, PHP7.3
- Removing compatibility with Postgis 2.0. Some spatial functions have been renamed to their
new names (example: ST_Line_Interpolate_Point has been renamed to ST_Line_Interpolate_Point).
- Removing test on Travis

## CrEOF/doctrine2-spatial [2.0.0] Version 2 - 2020-04-01

## CrEOF/doctrine2-spatial [2.0.0-RC1] Release candidat - 2020-03-26

### Added

- Geometric and geographic entities implements JsonSerialization.

## CrEOF/doctrine2-spatial[2.0.0-RC0] Release candidat - 2020-03-18

### Added

- A new documentation hosted on ReadTheDocs.
- Adding support of PHP7.2, PHP7.3, PHP7.4,
- Needed PHP extension added in composer.json,
- Spatial function implementing the ISO/IEC 13249-3:2016 or [OGC Standard](https://www.ogc.org/standards/sfs) are now stored in the [Standard](lib/LongitudeOne/Spatial/ORM/Query/AST/Functions/Standard) directory.
- Specific spatial function of the PostgreSql server are now store in the [PostgreSql](lib/LongitudeOne/Spatial/ORM/Query/AST/Functions/PostgreSql) directory.
- Specific spatial function of the PostgreSql server are now store in the [MySql](lib/LongitudeOne/Spatial/ORM/Query/AST/Functions/MySql) directory.
- Code coverage is now really at 90 percent. (CreOf code coverage was not valid because of AST functions which contained only properties),
- AST Functions updated to avoid misconfiguration (some properties was missing),
- AST Functions updated to detect which function was not tested,
- A lot of spatial functions,
- A lot of PostgreSql functions,
- Deprecated MySql functions replaced by their new names,
- Removing deprecations of doctrine2,
- Project forked from creof/doctrine-spatial2.

### Removed

- Removing support of PHP5.*, PHP7.0, PHP7.1

## CrEOF/doctrine2-spatial [1.1.1] - 2020-02-21

Nota: This version was never published by creof. But the fork begins at this date.

### Added

- Added support for PostgreSql ST_MakeEnvelope function.

### Changed

- Added implementation of getTypeFamily() and getSQLType() to AbstractGeometryType.
- Rename AbstractGeometryType class to AbstractSpatialType.
- Simplify logic in isClosed() method of AbstractLineString.
- Updated copyright year in LICENSE.

### Removed

- Unused imports from a number of classes.

## CrEOF/doctrine2-spatial [1.1] - 2015-12-20

### Added

- Local phpdocs to database platform classes.
- getMappedDatabaseTypes() method to PlatformInterface returning a unique type name used in type mapping.
- Entity and test for setting default SRID in column definition on PostgreSQL/PostGIS.
- Additional parameter to methods in PlatformInterface to pass DBAL type.
- Test class OrmMockTestCase with mocked DBAL connection.
- Test for Geography\Polygon type.
- Test for unsupported platforms.

### Changed

- Moved database platform classes to namespace LongitudeOne\Spatial\DBAL\Platform.
- Define exception messages where thrown in classes.
- Pass entity class names to usesEntity() in tests instead of looking them up in an array.
- Confirm types have not been previously added when setting up tests.
- Geometry and Geography platform classes unified in single class for each database platform.
- Class OrmTest renamed to OrmTestCase.
- Refactor single use methods in AbstractGeometryType into calling method.
- Include all test by default so tests are inadvertently skipped.
- Changed test class names to match filenames.

### Removed

- Static exception messages from package exception classes.
- getTypeFamily() method from PlatformInterface.
- Dependency on ramsey/array_column package.
- Empty test classes.

## CrEOF/doctrine2-spatial [1.0.1] - 2015-12-18

### Added

- Dependency on creof/geo-parser.
- Dependency on creof/wkt-parser.
- Dependency on creof/wkb-parser.
- Additional spatial functions support for PostgreSQL/PostGIS.

### Changed

- Replace regex in AbstractPoint with parser from creof/geo-parser.
- Use parser from creof/wkt-parser in AbstractPlatform class.
- Use parser from creof/wkb-parser in AbstractPlatform class.

### Removed

- StringLexer and StringParser classes no longer needed.
- BinaryReader, BinaryParser, and Utils classes no longer needed.
- Unused expection methods from InvalidValueException.

## CrEOF/doctrine2-spatial [1.0.0] - 2015-11-09

### Added

- Change log file to chronicle changes.
- Stub TODO.md file.
- CONTRIBUTING.md file with guidelines.
- CrEOF\Spatial\Tests\OrmTest class to remove dependency on doctrine/orm source for tests.
- Travis-CI repo hook and configuration.
- CodeClimate config.
- Test config flag "opt_mark_sql" to execute dummy query with test name before each test.
- Test config flag "opt_use_debug_stack" to use custom stack which logs queries.
- Numerous SQL/DQL functions for both PostgreSQL and MySQL.
- Coveralls config.
- MultiPolygon geometry DBAL type.

### Changed

- Minimum doctrine/orm version now 2.3.
- All ORM tests now extend CrEOF\Spatial\Tests\OrmTest.
- Specifying test platform through @group annotation has been deprecated. Tests now configure supported platforms in setUp(), unsupported tests are skipped.
- Cleaned up existing test classes.
- Replaced rhumsaa/array_column dev package dependency with ramsey/array_column. Prior has been abandoned and is no longer maintained.
- Tests now pass string values to parameters instead of objects to avoid issues with field value conversion.
- Documentation split up into multiple files.
- StringLexer and StringParser now correctly handle values with exponent/scientific notation.

### Removed

- AbstractDualGeometryDQLFunction, AbstractDualGeometryOptionalParameterDQLFunction, AbstractGeometryDQLFunction, AbstractSingleGeometryDQLFunction, AbstractTripleGeometryDQLFunction, and AbstractVariableGeometryDQLFunction classes.
