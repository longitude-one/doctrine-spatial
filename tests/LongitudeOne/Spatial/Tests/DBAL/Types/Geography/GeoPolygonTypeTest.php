<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP          8.1 | 8.2 | 8.3
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

namespace LongitudeOne\Spatial\Tests\DBAL\Types\Geography;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Exception\TypeNotRegistered;
use Doctrine\DBAL\Types\Type;
use LongitudeOne\Spatial\DBAL\Types\Geography\PolygonType;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geography\LineString;
use LongitudeOne\Spatial\PHP\Types\Geography\Point;
use LongitudeOne\Spatial\PHP\Types\Geography\Polygon;
use LongitudeOne\Spatial\Tests\Fixtures\GeoPolygonEntity;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * PolygonType tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group geography
 *
 * @internal
 *
 * @coversDefaultClass \LongitudeOne\Spatial\DBAL\Types\Geography\PolygonType
 */
class GeoPolygonTypeTest extends PersistOrmTestCase
{
    /**
     * Set up the test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::GEO_POLYGON_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        parent::setUp();
    }

    /**
     * Test the find by polygon method.
     *
     * @throws InvalidValueException when geometry contains an invalid value
     */
    public function testFindByPolygon(): void
    {
        $rings = [
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]),
        ];
        $entity = new GeoPolygonEntity();

        $entity->setPolygon(new Polygon($rings));
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $result = $this->getEntityManager()
            ->getRepository(get_class($entity))
            ->findByPolygon(new Polygon($rings))
        ;

        static::assertEquals($entity, $result[0]);
    }

    /**
     * Unit test the getName, getSQLType and getBindingType methods.
     *
     * @throws TypeNotRegistered It shall not happen
     */
    public function testName(): void
    {
        static::assertTrue(Type::hasType('geopolygon'));
        $spatialInstance = new PolygonType();
        static::assertNotFalse($spatialInstance->getName());
        static::assertSame('geopolygon', $spatialInstance->getName());
        static::assertSame(ParameterType::STRING, $spatialInstance->getBindingType());
        static::assertSame('Polygon', $spatialInstance->getSQLType());
    }

    /**
     * Test to store an empty polygon.
     */
    public function testNullPolygon(): void
    {
        $entity = new GeoPolygonEntity();
        static::assertIsRetrievableById($this->getEntityManager(), $entity);

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()->getRepository(self::GEO_POLYGON_ENTITY)->find($id);

        static::assertEquals($entity, $queryEntity);
    }

    /**
     * Test to store a polygon ring.
     *
     * @throws InvalidValueException when geometry contains an invalid value
     */
    public function testPolygonRing(): void
    {
        $rings = [
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]),
            new LineString([
                new Point(5, 5),
                new Point(7, 5),
                new Point(7, 7),
                new Point(5, 7),
                new Point(5, 5),
            ]),
        ];
        $entity = new GeoPolygonEntity();

        $entity->setPolygon(new Polygon($rings));
        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }

    /**
     * Test to store a solid polygon.
     *
     * @throws InvalidValueException when geometry contains an invalid value
     */
    public function testSolidPolygon(): void
    {
        $rings = [
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]),
        ];
        $entity = new GeoPolygonEntity();

        $entity->setPolygon(new Polygon($rings));
        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }
}
