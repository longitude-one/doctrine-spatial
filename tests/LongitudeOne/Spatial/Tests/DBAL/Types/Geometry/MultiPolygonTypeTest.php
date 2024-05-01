<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
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

namespace LongitudeOne\Spatial\Tests\DBAL\Types\Geometry;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\MultiPolygon;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\PHP\Types\Geometry\Polygon;
use LongitudeOne\Spatial\Tests\Fixtures\MultiPolygonEntity;
use LongitudeOne\Spatial\Tests\Helper\PersistHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;

/**
 * MultiPolygonType tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group geometry
 *
 * @internal
 *
 * @coversDefaultClass \LongitudeOne\Spatial\DBAL\Types\Geometry\MultiPolygonType
 */
class MultiPolygonTypeTest extends OrmTestCase
{
    use PersistHelperTrait;

    /**
     * Set up the test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::MULTIPOLYGON_ENTITY);
        $this->supportsPlatform(MySQLPlatform::class);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        parent::setUp();
    }

    /**
     * Test to store and find it by id then by polygon.
     *
     * @throws InvalidValueException when geometries are not valid
     */
    public function testMultiPolygon()
    {
        $polygons = [
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(0, 0),
                            new Point(10, 0),
                            new Point(10, 10),
                            new Point(0, 10),
                            new Point(0, 0),
                        ]
                    ),
                ]
            ),
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(5, 5),
                            new Point(7, 5),
                            new Point(7, 7),
                            new Point(5, 7),
                            new Point(5, 5),
                        ]
                    ),
                ]
            ),
        ];
        $entity = new MultiPolygonEntity();

        $entity->setMultiPolygon(new MultiPolygon($polygons));
        static::assertIsRetrievableById($this->getEntityManager(), $entity);

        $result = $this->getEntityManager()
            ->getRepository(self::MULTIPOLYGON_ENTITY)
            ->findByMultiPolygon(new MultiPolygon($polygons))
        ;

        static::assertEquals($entity, $result[0]);
    }

    /**
     * Test to store a null multipolygon and find it by id.
     */
    public function testNullMultiPolygon()
    {
        $entity = new MultiPolygonEntity();
        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }

    // TODO Try to find a null multiploygon
}
