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
use LongitudeOne\Spatial\PHP\Types\Geography\LineString;
use LongitudeOne\Spatial\PHP\Types\Geography\Point;
use LongitudeOne\Spatial\PHP\Types\Geography\Polygon;
use LongitudeOne\Spatial\Tests\Fixtures\GeographyEntity;
use LongitudeOne\Spatial\Tests\OrmTestCase;
use Doctrine\Persistence\Mapping\MappingException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Doctrine GeographyType tests.
 *
 * @group geography
 *
 * @internal
 * @coversDefaultClass \LongitudeOne\Spatial\DBAL\Types\GeographyType
 */
class GeographyTypeTest extends OrmTestCase
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
        $this->usesEntity(self::GEOGRAPHY_ENTITY);

        parent::setUp();
    }

    /**
     * Test to store and retrieve a geography composed by a linestring.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     */
    public function testLineStringGeography()
    {
        $entity = new GeographyEntity();

        $entity->setGeography(new LineString([
            new Point(0, 0),
            new Point(1, 1),
        ]));
        $this->storeAndRetrieve($entity);
    }

    /**
     * Test to store and retrieve a null geography.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     */
    public function testNullGeography()
    {
        $entity = new GeographyEntity();
        $this->storeAndRetrieve($entity);
    }

    /**
     * Test to store and retrieve a geography composed by a single point.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     */
    public function testPointGeography()
    {
        $entity = new GeographyEntity();

        $entity->setGeography(new Point(1, 1));
        $this->storeAndRetrieve($entity);
    }

    /**
     * Test to store and retrieve a geography composed by a polygon.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     */
    public function testPolygonGeography()
    {
        $entity = new GeographyEntity();

        $rings = [
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]),
        ];

        $entity->setGeography(new Polygon($rings));
        $this->storeAndRetrieve($entity);
    }

    /**
     * Store and retrieve geography entity in database.
     * Then assert data are equals, not same.
     *
     * @param GeographyEntity $entity Entity to test
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     */
    private function storeAndRetrieve(GeographyEntity $entity)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()->getRepository(self::GEOGRAPHY_ENTITY)->find($id);

        static::assertEquals($entity, $queryEntity);
    }
}
