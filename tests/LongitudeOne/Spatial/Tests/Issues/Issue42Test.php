<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
 * Doctrine ORM 2.19 | 3.1
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2024
 * Copyright Longitude One 2020-2024
 * Copyright 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\Spatial\Tests\Issues;

use LongitudeOne\Spatial\DBAL\Platform\MySql;
use LongitudeOne\Spatial\DBAL\Platform\PostgreSql;
use LongitudeOne\Spatial\DBAL\Types\AbstractSpatialType;
use LongitudeOne\Spatial\DBAL\Types\Geography\PointType;
use LongitudeOne\Spatial\DBAL\Types\GeographyType;
use LongitudeOne\Spatial\DBAL\Types\Geometry\LineStringType;
use LongitudeOne\Spatial\DBAL\Types\GeometryType;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\MissingArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Issue 42 test.
 *
 * @see https://github.com/longitude-one/doctrine-spatial/issues/42
 *
 * @internal
 *
 * @group php
 *
 * @coversNothing
 */
class Issue42Test extends TestCase
{
    /**
     * Test issue with MySQL.
     *
     * @throws MissingArgumentException this exception shall happen
     */
    public function testIssueWithMySql(): void
    {
        $platform = new MySql();

        static::assertSame('GEOMETRY', $platform->getSqlDeclaration($this->getGeometryDumpFromOldVersion()));
        static::assertSame('GEOMETRY', $platform->getSqlDeclaration($this->getGeographyDumpFromOldVersion()));

        $actual = $platform->getSqlDeclaration($this->getGeometryDumpFromNewVersion(), new LineStringType());
        static::assertSame('LINESTRING', $actual);

        $actual = $platform->getSqlDeclaration($this->getGeographyDumpFromNewVersion(), new GeographyType());
        static::assertSame('GEOMETRY', $actual);

        self::expectException(MissingArgumentException::class);
        $platform->getSqlDeclaration($this->getGeographyDumpFromNewVersion());
    }

    /**
     * Test issue with PostgreSQL.
     *
     * @throws MissingArgumentException this exception shall happen
     * @throws InvalidValueException    this exception shall not happen
     */
    public function testIssueWithPgSql(): void
    {
        $platform = new PostgreSql();

        static::assertSame('Geometry', $platform->getSqlDeclaration($this->getGeometryDumpFromOldVersion()));
        static::assertSame('Geography', $platform->getSqlDeclaration($this->getGeographyDumpFromOldVersion()));

        $actual = $platform->getSqlDeclaration($this->getGeometryDumpFromNewVersion(), new LineStringType());
        static::assertSame('Geometry(LineString)', $actual);

        $actual = $platform->getSqlDeclaration($this->getGeographyDumpFromNewVersion(), new PointType());
        static::assertSame('Geography(Point)', $actual);

        self::expectException(MissingArgumentException::class);
        $platform->getSqlDeclaration($this->getGeographyDumpFromNewVersion());
    }

    /**
     * Return the $fieldDeclaration dump from doctrine/dbal 3.7+ for a GeographyType.
     *
     * @return array{length: int, unsigned: bool, fixed: bool, default: null, notnull: bool, scale: ?int, precision: ?int, autoincrement: bool} the dump
     */
    private function getGeographyDumpFromNewVersion(): array
    {
        return [
            'length' => 0,
            'unsigned' => false,
            'fixed' => false,
            'default' => null,
            'notnull' => true,
            'scale' => null,
            'precision' => null,
            'autoincrement' => false,
        ];
    }

    /**
     * Return the $fieldDeclaration dump from doctrine/dbal 3.6- for a GeographyType.
     *
     * @return array{name: string, type: AbstractSpatialType, length: int, comment:string, default: null, notnull: bool, scale: int, precision: int, autoincrement: bool, fixed: bool, unsigned: bool, version:bool} the dump
     */
    private function getGeographyDumpFromOldVersion(): array
    {
        return [
            'name' => 'latlng',
            'type' => new GeographyType(),
            'default' => null,
            'notnull' => true,
            'length' => 0,
            'precision' => 10,
            'scale' => 0,
            'fixed' => false,
            'unsigned' => false,
            'autoincrement' => false,
            'comment' => '(DC2Type:geography)',
            'version' => false,
        ];
    }

    /**
     * Return the $fieldDeclaration dump from doctrine/dbal 3.7+ for a GeometryType.
     *
     * @return array{length: int, unsigned: bool, fixed: bool, default: null, notnull: bool, scale: ?int, precision: ?int, autoincrement: bool} the dump
     */
    private function getGeometryDumpFromNewVersion(): array
    {
        return $this->getGeographyDumpFromNewVersion();
    }

    /**
     * Return the $fieldDeclaration dump from doctrine/dbal 3.6- for a GeometryType.
     *
     * @return array{name: string, type: AbstractSpatialType, length: int, comment:string, default: null, notnull: bool, scale: int, precision: int, autoincrement: bool, fixed: bool, unsigned: bool, version:bool} the dump
     */
    private function getGeometryDumpFromOldVersion(): array
    {
        $dump = $this->getGeographyDumpFromOldVersion();
        $dump['type'] = new GeometryType();
        $dump['comment'] = '(DC2Type:geometry)';

        return $dump;
    }
}
