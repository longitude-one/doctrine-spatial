<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2024
 * Copyright Longitude One 2020-2024
 * Copyright 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace LongitudeOne\Spatial\Tests\Issues;

use LongitudeOne\Spatial\DBAL\Platform\MySql;
use LongitudeOne\Spatial\DBAL\Platform\PostgreSql;
use LongitudeOne\Spatial\DBAL\Types\GeographyType;
use LongitudeOne\Spatial\DBAL\Types\GeometryType;
use PHPUnit\Framework\TestCase;

/**
 * Issue 42 test.
 *
 * @see https://github.com/longitude-one/doctrine-spatial/issues/42
 *
 * @internal
 *
 * @coversNothing
 */
class Issue42Test extends TestCase
{
    /**
     * Test issue with MySQL.
     */
    public function testIssueWithMySql(): void
    {
        $platform = new MySql();

        static::assertSame('GEOMETRY', $platform->getSqlDeclaration($this->getGeometryDumpFromNewVersion()));
        static::assertSame('GEOMETRY', $platform->getSqlDeclaration($this->getGeographyDumpFromNewVersion()));

        static::assertSame('GEOMETRY', $platform->getSqlDeclaration($this->getGeometryDumpFromOldVersion()));
        static::assertSame('GEOMETRY', $platform->getSqlDeclaration($this->getGeographyDumpFromOldVersion()));
    }

    /**
     * Test issue with PostgreSQL.
     */
    public function testIssueWithPgSql(): void
    {
        $platform = new PostgreSql();

        static::assertSame('GEOMETRY', $platform->getSqlDeclaration($this->getGeometryDumpFromNewVersion()));
        static::assertSame('GEOGRAPHY', $platform->getSqlDeclaration($this->getGeographyDumpFromNewVersion()));

        static::assertSame('GEOMETRY', $platform->getSqlDeclaration($this->getGeometryDumpFromOldVersion()));
        static::assertSame('GEOGRAPHY', $platform->getSqlDeclaration($this->getGeographyDumpFromOldVersion()));
    }

    /**
     * Return the $fieldDeclaration dump from doctrine/dbal 3.7+ for a GeographyType.
     *
     * @return array the dump
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
            'comment' => '(DC2Type:geography)',
        ];
    }

    /**
     * Return the $fieldDeclaration dump from doctrine/dbal 3.6- for a GeographyType.
     *
     * @return array the dump
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
     * @return array the dump
     */
    private function getGeometryDumpFromNewVersion(): array
    {
        $dump = $this->getGeographyDumpFromNewVersion();
        $dump['comment'] = '(DC2Type:geometry)';

        return $dump;
    }

    /**
     * Return the $fieldDeclaration dump from doctrine/dbal 3.6- for a GeometryType.
     *
     * @return array the dump
     */
    private function getGeometryDumpFromOldVersion(): array
    {
        $dump = $this->getGeographyDumpFromOldVersion();
        $dump['type'] = new GeometryType();
        $dump['comment'] = '(DC2Type:geometry)';

        return $dump;
    }
}
