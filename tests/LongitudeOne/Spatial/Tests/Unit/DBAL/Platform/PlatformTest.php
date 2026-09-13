<?php
/**
 * This file is part of the Doctrine Spatial extension.
 *
 * PHP 8.4 | 8.5
 * Doctrine ORM ^3.6
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2026
 * Copyright Longitude One 2020-2026
 * Copyright 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\Spatial\Tests\Unit\DBAL\Platform;

use LongitudeOne\Geo\WKB\Exception\ExceptionInterface as WkbExceptionInterface;
use LongitudeOne\Spatial\DBAL\Platform\AbstractPlatform;
use LongitudeOne\Spatial\DBAL\Platform\MariaDB;
use LongitudeOne\Spatial\DBAL\Platform\MySql;
use LongitudeOne\Spatial\DBAL\Platform\PlatformInterface;
use LongitudeOne\Spatial\DBAL\Platform\PostgreSql;
use LongitudeOne\Spatial\DBAL\Platform\SqlServer;
use LongitudeOne\Spatial\DBAL\Types\AbstractSpatialType;
use LongitudeOne\Spatial\DBAL\Types\Geography\PointType as GeographyPointType;
use LongitudeOne\Spatial\DBAL\Types\GeographyType;
use LongitudeOne\Spatial\DBAL\Types\Geometry\LineStringType;
use LongitudeOne\Spatial\DBAL\Types\Geometry\PointType as GeometryPointType;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\MissingArgumentException;
use LongitudeOne\Spatial\Exception\UnsupportedTypeException;
use LongitudeOne\Spatial\PHP\Types\Geography\Point as GeographyPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point as GeometryPoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Spatial platform unit tests.
 *
 * @internal
 */
#[CoversClass(AbstractPlatform::class)]
#[CoversClass(MariaDB::class)]
#[CoversClass(MySql::class)]
#[CoversClass(PostgreSql::class)]
#[CoversClass(SqlServer::class)]
#[Group('php')]
class PlatformTest extends TestCase
{
    /**
     * Test conversion to database strings.
     */
    public function testDatabaseValueConversions(): void
    {
        $geometryType = new GeometryPointType();
        $geometry = new GeometryPoint(1, 2);

        foreach ([new MariaDB(), new MySql(), new PostgreSql(), new SqlServer()] as $platform) {
            static::assertSame('POINT(1 2)', $platform->convertToDatabaseValue($geometryType, $geometry));
        }

        $geography = new GeographyPoint(1, 2);

        static::assertSame('SRID=4326;POINT(1 2)', (new PostgreSql())->convertToDatabaseValue(new GeographyType(), $geography));
        static::assertSame(4326, $geography->getSrid());
    }

    /**
     * Test generic and platform-specific function declarations.
     */
    public function testFunctionDeclarations(): void
    {
        static::assertSame('ST_Buffer(column, 1)', (new MariaDB())->getFunctionSqlDeclaration('ST_Buffer', ['column', '1']));
        static::assertSame('ST_SRID(column, 4326)', (new MySql())->getFunctionSqlDeclaration('ST_SetSRID', ['column', '4326']));

        $platform = new SqlServer();
        static::assertSame('(column).STX', $platform->getFunctionSqlDeclaration('ST_X', ['column']));
        static::assertSame('geometry::STGeomFromText(column, 0)', $platform->getFunctionSqlDeclaration('ST_GeomFromText', ['column']));
        static::assertSame('geometry::STLineFromWKB(column)', $platform->getFunctionSqlDeclaration('ST_LineStringFromWKB', ['column']));
        static::assertSame('geography::STGeomFromText(column, 4326)', $platform->getFunctionSqlDeclaration('ST_GeographyFromText', ['column']));
        static::assertSame('(column).STLength()', $platform->getFunctionSqlDeclaration('ST_Perimeter', ['column']));
        static::assertSame('(column).STSrid', $platform->getFunctionSqlDeclaration('ST_SRID', ['column']));
        static::assertSame('(column).STArea()', $platform->getFunctionSqlDeclaration('ST_Area', ['column']));
    }

    /**
     * Test geography column declarations.
     */
    public function testGeographyDeclarations(): void
    {
        $type = new GeographyType();

        static::assertSame('GEOMETRY', (new MariaDB())->getSqlDeclaration([], $type));
        static::assertSame('GEOMETRY', (new MySql())->getSqlDeclaration([], $type));
        static::assertSame('Geography', (new PostgreSql())->getSqlDeclaration([], $type, 4326));
        static::assertSame('GEOGRAPHY', (new SqlServer())->getSqlDeclaration([], $type));
        static::assertSame('Geometry(Point,4326)', (new PostgreSql())->getSqlDeclaration([], new GeometryPointType(), 4326));
    }

    /**
     * Test SQL conversions for geography values.
     *
     * @param PlatformInterface $platform    Spatial platform
     * @param string            $databaseSql Expected database conversion SQL
     * @param string            $phpSql      Expected PHP conversion SQL
     */
    #[DataProvider('geographySqlConversionProvider')]
    public function testGeographySqlConversions(PlatformInterface $platform, string $databaseSql, string $phpSql): void
    {
        $type = new GeographyType();

        static::assertSame($databaseSql, $platform->convertToDatabaseValueSql($type, 'column'));
        static::assertSame($phpSql, $platform->convertToPhpValueSql($type, 'column'));
    }

    /**
     * @return iterable<string, array{PlatformInterface, string, string}>
     */
    public static function geographySqlConversionProvider(): iterable
    {
        yield 'MariaDB' => [new MariaDB(), 'ST_GeomFromText(column)', 'ST_AsBinary(column)'];

        yield 'MySQL' => [new MySql(), 'ST_GeomFromText(column)', 'ST_AsBinary(column)'];

        yield 'PostgreSQL' => [new PostgreSql(), 'ST_GeographyFromText(column)', 'ST_AsEWKT(column)'];

        yield 'SQL Server' => [new SqlServer(), 'geography::STGeomFromText(column, 4326)', 'column.STAsBinary()'];
    }

    /**
     * Test geometry column declarations.
     *
     * @param PlatformInterface $platform Spatial platform
     * @param string            $expected Expected SQL declaration
     */
    #[DataProvider('declarationProvider')]
    public function testGeometryDeclarations(PlatformInterface $platform, string $expected): void
    {
        static::assertSame($expected, $platform->getSqlDeclaration([], new GeometryPointType()));
    }

    /**
     * @return iterable<string, array{PlatformInterface, string}>
     */
    public static function declarationProvider(): iterable
    {
        yield 'MariaDB geometry' => [new MariaDB(), 'POINT'];

        yield 'MySQL geometry' => [new MySql(), 'POINT'];

        yield 'PostgreSQL geometry' => [new PostgreSql(), 'Geometry(Point)'];

        yield 'SQL Server geometry' => [new SqlServer(), 'GEOMETRY'];
    }

    /**
     * Test SQL conversions for geometry values.
     *
     * @param PlatformInterface $platform    Spatial platform
     * @param string            $databaseSql Expected database conversion SQL
     * @param string            $phpSql      Expected PHP conversion SQL
     */
    #[DataProvider('sqlConversionProvider')]
    public function testGeometrySqlConversions(PlatformInterface $platform, string $databaseSql, string $phpSql): void
    {
        $type = new GeometryPointType();

        static::assertSame($databaseSql, $platform->convertToDatabaseValueSql($type, 'column'));
        static::assertSame($phpSql, $platform->convertToPhpValueSql($type, 'column'));
    }

    /**
     * @return iterable<string, array{PlatformInterface, string, string}>
     */
    public static function sqlConversionProvider(): iterable
    {
        yield 'MariaDB' => [new MariaDB(), 'ST_GeomFromText(column)', 'ST_AsBinary(column)'];

        yield 'MySQL' => [new MySql(), 'ST_GeomFromText(column)', 'ST_AsBinary(column)'];

        yield 'PostgreSQL' => [new PostgreSql(), 'ST_GeomFromEWKT(column)', 'ST_AsEWKB(column)'];

        yield 'SQL Server' => [new SqlServer(), 'geometry::STGeomFromText(column, 0)', 'column.STAsBinary()'];
    }

    /**
     * Test PHP value conversions and mapped database type names.
     */
    public function testPhpValueConversionsAndMappedTypes(): void
    {
        $platform = new PostgreSql();
        $geometryType = new GeometryPointType();

        static::assertSame('1 2', (string) $platform->convertStringToPhpValue($geometryType, 'POINT(1 2)'));
        static::assertSame(['point'], $platform->getMappedDatabaseTypes($geometryType));
        static::assertSame(['geography(point)'], $platform->getMappedDatabaseTypes(new GeographyPointType()));
        static::assertSame('1 2,3 4', (string) $platform->convertStringToPhpValue(new LineStringType(), 'LINESTRING(1 2, 3 4)'));

        $binaryPoint = pack('CVee', 1, 1, 1.0, 2.0);
        $resource = fopen('php://memory', 'r+b');
        static::assertIsResource($resource);
        fwrite($resource, $binaryPoint);
        rewind($resource);

        try {
            static::assertSame('1 2', (string) $platform->convertBinaryToPhpValue($geometryType, $resource));
        } finally {
            fclose($resource);
        }
    }

    /**
     * Test failures while reading binary spatial data.
     */
    public function testRejectsInvalidBinaryValue(): void
    {
        $this->expectException(WkbExceptionInterface::class);

        $invalidBinaryValue = pack('CV', 1, 21);

        (new PostgreSql())->convertBinaryToPhpValue(new GeometryPointType(), $invalidBinaryValue);
    }

    /**
     * Test failures for unknown parsed spatial types.
     */
    public function testRejectsUnknownParsedSpatialType(): void
    {
        $method = new \ReflectionMethod(AbstractPlatform::class, 'newObjectFromValue');

        $this->expectException(InvalidValueException::class);

        $method->invoke(new PostgreSql(), new GeometryPointType(), ['type' => 'Unknown', 'value' => []]);
    }

    /**
     * Test mapped database type validation.
     */
    public function testRejectsUnsupportedMappedDatabaseType(): void
    {
        $type = new class extends AbstractSpatialType {
            /**
             * @return class-string<PlatformInterface>[]
             */
            protected function getSupportedPlatforms(): array
            {
                return [];
            }
        };

        $this->expectException(UnsupportedTypeException::class);

        (new PostgreSql())->getMappedDatabaseTypes($type);
    }

    /**
     * Test the exceptions raised for unsupported spatial types.
     *
     * @param PlatformInterface $platform Spatial platform
     */
    #[DataProvider('platformProvider')]
    public function testRejectsUnsupportedSpatialType(PlatformInterface $platform): void
    {
        $type = new class extends AbstractSpatialType {
            /**
             * @return class-string<PlatformInterface>[]
             */
            protected function getSupportedPlatforms(): array
            {
                return [];
            }
        };

        $this->expectException(UnsupportedTypeException::class);

        $platform->convertToDatabaseValue($type, new GeometryPoint(1, 2));
    }

    /**
     * @return iterable<string, array{PlatformInterface}>
     */
    public static function platformProvider(): iterable
    {
        yield 'MariaDB' => [new MariaDB()];

        yield 'MySQL' => [new MySql()];

        yield 'PostgreSQL' => [new PostgreSql()];

        yield 'SQL Server' => [new SqlServer()];
    }

    /**
     * Test argument validation inherited from the abstract platform.
     */
    public function testValidatesPlatformArguments(): void
    {
        $this->expectException(MissingArgumentException::class);

        (new MariaDB())->getSqlDeclaration([]);
    }

    /**
     * Test SRID validation inherited from the abstract platform.
     */
    public function testValidatesSrid(): void
    {
        $this->expectException(InvalidValueException::class);

        (new PostgreSql())->getSqlDeclaration(['srid' => []], new GeometryPointType());
    }
}
