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

namespace LongitudeOne\Spatial\Tests\DBAL\Types\Geometry;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Exception\TypeNotRegistered;
use Doctrine\DBAL\Types\Type;
use LongitudeOne\Spatial\DBAL\Types\Geometry\PolygonType;
use LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity;
use LongitudeOne\Spatial\Tests\Helper\LineStringHelperTrait;
use LongitudeOne\Spatial\Tests\Helper\PersistantPolygonHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * PolygonType tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group geometry
 *
 * @internal
 *
 * @coversDefaultClass \LongitudeOne\Spatial\DBAL\Types\Geometry\PolygonType
 */
class PolygonTypeTest extends PersistOrmTestCase
{
    use LineStringHelperTrait;
    use PersistantPolygonHelperTrait;

    /**
     * Set up the test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->supportsPlatform(MariaDBPlatform::class);
        $this->supportsPlatform(MySQLPlatform::class);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        parent::setUp();
    }

    /**
     * Test to store a polygon and find it by its geometric.
     */
    public function testFindByPolygon(): void
    {
        $polygon = $this->createBigPolygon();
        $entity = $this->persistPolygon($polygon);
        $result = $this->getEntityManager()->getRepository(PolygonEntity::class)->findByPolygon($polygon);

        static::assertCount(1, $result);
        static::assertEquals($entity, $result[0]);
    }

    /**
     * Unit test the getName, getSQLType and getBindingType methods.
     *
     * @throws TypeNotRegistered It shall not happen
     */
    public function testName(): void
    {
        if (!Type::hasType('polygon')) {
            Type::addType('polygon', PolygonType::class);
        }

        $spatialInstance = new PolygonType();
        static::assertNotFalse($spatialInstance->getName());
        static::assertSame('polygon', $spatialInstance->getName());
        static::assertSame(ParameterType::STRING, $spatialInstance->getBindingType());
        static::assertSame('Polygon', $spatialInstance->getSQLType());
    }

    /**
     * Test to store a null polygon and find it by its id.
     */
    public function testNullPolygon(): void
    {
        $entity = new PolygonEntity();

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $id = $entity->getId();

        $queryEntity = $this->getEntityManager()->getRepository(self::POLYGON_ENTITY)->find($id);

        static::assertEquals($entity, $queryEntity);
    }

    /**
     * Test to store a polygon ring and find it by its id.
     */
    public function testPolygonRing(): void
    {
        $entity = $this->persistHoleyPolygon();
        $id = $entity->getId();
        $queryEntity = $this->getEntityManager()->getRepository(self::POLYGON_ENTITY)->find($id);

        static::assertEquals($entity, $queryEntity);
    }

    /**
     * Test to store a solid polygon and find it by its id.
     */
    public function testSolidPolygon(): void
    {
        $entity = $this->persistBigPolygon();
        $id = $entity->getId();
        $queryEntity = $this->getEntityManager()->getRepository(self::POLYGON_ENTITY)->find($id);

        static::assertEquals($entity, $queryEntity);
    }
}
