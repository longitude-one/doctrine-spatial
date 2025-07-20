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

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Generator;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geography\Point as GeographicPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point as GeometricPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString as GeometricLineString;
use LongitudeOne\Spatial\Tests\Fixtures\GeoPointSridEntity;
use LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity;
use LongitudeOne\Spatial\Tests\Fixtures\PointEntity;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Issue 42 test.
 *
 * @see https://github.com/longitude-one/doctrine-spatial/issues/17
 *
 * @internal
 *
 * @group php
 *
 * @coversNothing
 */
class Issue17Test extends PersistOrmTestCase
{
    /**
     * @return Generator<string, object[], null, void>
     *
     * @throws InvalidValueException this should not happen, we provide only valid values.
     */
    public static function entityProvider(): Generator
    {
        $point = new GeometricPoint(42, 42);
        $point->setSrid(4326);

        $pointEntity = new PointEntity();
        $pointEntity->setPoint($point);

        yield 'Geometric point with SRID' => [$pointEntity];

        $anotherPointWithSrid = new GeometricPoint(43, 43);
        $anotherPointWithSrid->setSrid(4326);

        $lineString = new GeometricLineString([$point, $anotherPointWithSrid]);
        $lineString->setSrid(4326);
        $lineStringEntity = new LineStringEntity();
        $lineStringEntity->setLineString($lineString);

        yield 'Geometric LineString with SRID' => [$pointEntity];

        $point = new GeographicPoint(42, 42);
        $point->setSrid(4326);
        $pointEntity = new GeoPointSridEntity();
        $pointEntity->setPoint($point);

        yield 'Geographic point with SRID' => [$pointEntity];
    }

    /**
     * Set up the test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->usesEntity(self::GEO_POINT_SRID_ENTITY);
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->supportsPlatform(MySQLPlatform::class);
        // This test was only failing on MySQL Platform, but let's check PostGreSQL too.
        $this->supportsPlatform(PostgreSQLPlatform::class);
        parent::setUp();
    }

    /**
     * Test issue with MySQL.
     */
    #[DataProvider('entityProvider')]
    public function testToPersistPointWithSrid(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $result = $this->getEntityManager()->getRepository($entity::class)->findBy(['id' => $entity->getId()]);

        static::assertEquals($entity, $result[0]);
    }
}
