<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 7.4 | 8.0
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017 - 2021
 * (c) Longitude One 2020 - 2021
 * (c) 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace LongitudeOne\Spatial\Tests\DBAL\Types;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\PHP\Types\Geometry\Polygon;
use LongitudeOne\Spatial\Tests\Fixtures\GeometryEntity;
use LongitudeOne\Spatial\Tests\Fixtures\NoHintGeometryEntity;
use LongitudeOne\Spatial\Tests\OrmTestCase;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Doctrine GeometryType tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group geometry
 *
 * @internal
 * @coversDefaultClass \LongitudeOne\Spatial\DBAL\Types\GeometryType
 */
class GeometryTypeTest extends OrmTestCase
{
    /**
     * Setup the geography type test.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::GEOMETRY_ENTITY);
        $this->usesEntity(self::NO_HINT_GEOMETRY_ENTITY);
        parent::setUp();
    }

    /**
     * When I store a bad geometry an Invalid value exception shall be thrown.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws OptimisticLockException      when clear fails
     */
    public function testBadGeometryValue()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Geometry column values must implement GeometryInterface');

        $entity = new NoHintGeometryEntity();

        $entity->setGeometry('POINT(0 0)');
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Test to store a line string geometry and retrieve it by its identifier.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     */
    public function testLineStringGeometry()
    {
        $entity = new GeometryEntity();

        $entity->setGeometry(new LineString([
            new Point(0, 0),
            new Point(1, 1),
        ]));
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()->getRepository(self::GEOMETRY_ENTITY)->find($id);

        static::assertEquals($entity, $queryEntity);
    }

    /**
     * Test to store a null geometry and retrieve it by its identifier.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     */
    public function testNullGeometry()
    {
        $entity = new GeometryEntity();

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()->getRepository(self::GEOMETRY_ENTITY)->find($id);

        static::assertEquals($entity, $queryEntity);
    }

    /**
     * Test to store a point geometry and retrieve it by its identifier.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     */
    public function testPointGeometry()
    {
        $entity = new GeometryEntity();

        $entity->setGeometry(new Point(1, 1));
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()->getRepository(self::GEOMETRY_ENTITY)->find($id);

        static::assertEquals($entity, $queryEntity);
    }

    /**
     * Test to store a point geometry with its SRID and retrieve it by its identifier.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group srid
     */
    public function testPointGeometryWithSrid()
    {
        $entity = new GeometryEntity();
        $point = new Point(1, 1);

        $point->setSrid(200);
        $entity->setGeometry($point);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()->getRepository(self::GEOMETRY_ENTITY)->find($id);

        static::assertEquals($entity, $queryEntity);
    }

    /**
     * Test to store a point geometry without SRID and retrieve it by its identifier.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group srid
     */
    public function testPointGeometryWithZeroSrid()
    {
        $entity = new GeometryEntity();
        $point = new Point(1, 1);

        $point->setSrid(0);
        $entity->setGeometry($point);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()->getRepository(self::GEOMETRY_ENTITY)->find($id);

        static::assertEquals($entity, $queryEntity);
    }

    /**
     * Test to store a polygon geometry and retrieve it by its identifier.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     */
    public function testPolygonGeometry()
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
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()->getRepository(self::GEOMETRY_ENTITY)->find($id);

        static::assertEquals($entity, $queryEntity);
    }

    /**
     * Test to store a polygon geometry with SRID and retrieve it by its identifier.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group srid
     */
    public function testPolygonGeometryWithSrid()
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

        $polygon = new Polygon($rings);
        $polygon->setSrid(4326);
        $entity->setGeometry($polygon);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()->getRepository(self::GEOMETRY_ENTITY)->find($id);

        static::assertEquals($entity, $queryEntity);
    }
}
