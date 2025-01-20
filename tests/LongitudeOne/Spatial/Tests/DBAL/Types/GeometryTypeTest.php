<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
 * Doctrine ORM 2.19 | 3.1
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2025
 * Copyright Longitude One 2020-2025
 * Copyright 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\Spatial\Tests\DBAL\Types;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Exception\TypeNotRegistered;
use Doctrine\DBAL\Types\Type;
use LongitudeOne\Spatial\DBAL\Types\GeometryType;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geometry\GeometryInterface;
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\PHP\Types\Geometry\Polygon;
use LongitudeOne\Spatial\Tests\Fixtures\GeometryEntity;
use LongitudeOne\Spatial\Tests\Fixtures\NoHintGeometryEntity;
use LongitudeOne\Spatial\Tests\Helper\PersistantGeometryHelperTrait;
use LongitudeOne\Spatial\Tests\Helper\PersistantPolygonHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * Doctrine GeometryType tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group geometry
 *
 * @internal
 *
 * @coversDefaultClass \LongitudeOne\Spatial\DBAL\Types\GeometryType
 */
class GeometryTypeTest extends PersistOrmTestCase
{
    use PersistantGeometryHelperTrait;
    use PersistantPolygonHelperTrait;

    /**
     * Set up the geography type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::GEOMETRY_ENTITY);
        $this->usesEntity(self::NO_HINT_GEOMETRY_ENTITY);
        $this->supportsPlatform(MySQLPlatform::class);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        parent::setUp();
    }

    /**
     * When I store a bad geometry an Invalid value exception shall be thrown.
     */
    public function testBadGeometryValue(): void
    {
        static::expectException(InvalidValueException::class);
        static::expectExceptionMessage('Spatial column values must implement SpatialInterface');

        $entity = new NoHintGeometryEntity();
        $entity->setGeometry('POINT(0 0)');
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Test to store a line string geometry and retrieve it by its identifier.
     */
    public function testLineStringGeometry(): void
    {
        $entity = $this->persistGeometryStraightLine();
        static::assertIsRetrievableById($this->getEntityManager(), $entity);
        static::assertInstanceOf(GeometryInterface::class, $entity->getGeometry());
    }

    /**
     * Unit test the getName, getSQLType and getBindingType methods.
     *
     * @throws TypeNotRegistered It shall not happen
     */
    public function testName(): void
    {
        if (!Type::hasType('geometry')) {
            Type::addType('geometry', GeometryType::class);
        }

        $spatialInstance = new GeometryType();
        static::assertNotFalse($spatialInstance->getName());
        static::assertSame('geometry', $spatialInstance->getName());
        static::assertSame(ParameterType::STRING, $spatialInstance->getBindingType());
        static::assertSame('Geometry', $spatialInstance->getSQLType());
    }

    /**
     * Test to store a null geometry and retrieve it by its identifier.
     */
    public function testNullGeometry(): void
    {
        $entity = $this->persistNullGeometry();
        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }

    /**
     * Test to persist a point geometry and retrieve it by its identifier.
     */
    public function testPointGeometry(): void
    {
        $entity = $this->persistGeometryO();
        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }

    /**
     * Test to store a point geometry with its SRID and retrieve it by its identifier.
     *
     * @group srid
     */
    public function testPointGeometryWithSrid(): void
    {
        $entity = $this->persistGeometryA(200);
        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }

    /**
     * Test to store a point geometry without SRID and retrieve it by its identifier.
     *
     * @group srid
     */
    public function testPointGeometryWithZeroSrid(): void
    {
        $entity = $this->persistGeometryA(0);

        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }

    /**
     * Test to persist a polygon geometry and retrieve it by its identifier.
     *
     * @throws InvalidValueException when geometries are not valid
     */
    public function testPolygonGeometry(): void
    {
        $entity = new GeometryEntity();
        $rings = [
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]),
        ];

        $entity->setGeometry(new Polygon($rings));
        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }

    /**
     * Test to store a polygon geometry with SRID and retrieve it by its identifier.
     *
     * @group srid
     */
    public function testPolygonGeometryWithSrid(): void
    {
        $entity = new GeometryEntity();

        $polygon = $this->createBigPolygon();
        $polygon->setSrid(4326);
        $entity->setGeometry($polygon);

        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }
}
