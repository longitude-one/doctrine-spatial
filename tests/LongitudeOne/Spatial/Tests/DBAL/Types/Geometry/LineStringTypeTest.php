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

namespace LongitudeOne\Spatial\Tests\DBAL\Types\Geometry;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity;
use LongitudeOne\Spatial\Tests\OrmTestCase;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Doctrine LineStringType tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group geometry
 *
 * @internal
 * @coversDefaultClass \LongitudeOne\Spatial\DBAL\Types\Geometry\LineStringType
 */
class LineStringTypeTest extends OrmTestCase
{
    /**
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        parent::setUp();
    }

    /**
     * Test to store and find a line string in table.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     */
    public function testFindByLineString()
    {
        $lineString = new LineString(
            [
                new Point(0, 0),
                new Point(1, 1),
                new Point(2, 2),
            ]
        );
        $entity = new LineStringEntity();

        $entity->setLineString($lineString);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $this->getEntityManager()->clear();

        $result = $this->getEntityManager()
            ->getRepository(self::LINESTRING_ENTITY)
            ->findByLineString($lineString)
        ;

        static::assertEquals($entity, $result[0]);
    }

    /**
     * Test to store and find it by id.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     * @throws InvalidValueException        when geometries are not valid
     */
    public function testLineString()
    {
        $lineString = new LineString(
            [
                new Point(0, 0),
                new Point(1, 1),
                new Point(2, 2),
            ]
        );
        $entity = new LineStringEntity();

        $entity->setLineString($lineString);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()->getRepository(self::LINESTRING_ENTITY)->find($id);

        static::assertEquals($entity, $queryEntity);
    }

    /**
     * Test to store a null line string, then to find it with its id.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws MappingException             when mapping
     * @throws OptimisticLockException      when clear fails
     */
    public function testNullLineStringType()
    {
        $entity = new LineStringEntity();

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $this->getEntityManager()->clear();

        $queryEntity = $this->getEntityManager()->getRepository(self::LINESTRING_ENTITY)->find($id);

        static::assertEquals($entity, $queryEntity);
    }

    //TODO test to find all null linestring
}
