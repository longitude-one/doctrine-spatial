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

namespace LongitudeOne\Spatial\Tests\DBAL\Types\Geometry;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Exception\TypeNotRegistered;
use Doctrine\DBAL\Types\Type;
use LongitudeOne\Spatial\DBAL\Types\Geometry\Point3DType;
use LongitudeOne\Spatial\DBAL\Types\Geometry\PointType;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point3D;
use LongitudeOne\Spatial\Tests\Fixtures\Point3DEntity;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * Doctrine PointType tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group geometry
 *
 * @internal
 *
 * @coversDefaultClass \LongitudeOne\Spatial\DBAL\Types\Geometry\PointType
 */
class Point3DTypeTest extends PersistOrmTestCase
{
    use PersistantPointHelperTrait;

    /**
     * Set up the test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT3D_ENTITY);
        // Three-dimensional point doesn't exist on MySQL
        // $this->supportsPlatform(MySQLPlatform::class);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        parent::setUp();
    }

    /**
     * Test the declaration type.
     */
    public function testDeclarationType(): void
    {
        $this->usesEntity(self::POINT3D_ENTITY);
        $metadata = $this->getEntityManager()->getClassMetadata(Point3DEntity::class);

        // Set the type
        $type = null;
        if (is_array($metadata->getFieldMapping('point'))) {
            // doctrine/orm:2.9
            $type = $metadata->getFieldMapping('point')['type'];
        }
        if (is_object($metadata->getFieldMapping('point'))) {
            // doctrine/orm:3.1, doctrine/orm:4.0
            $type = $metadata->getFieldMapping('point')->type;
        }

        // Check the type
        static::assertNotNull($type, 'This test is not compatible with this version of doctrine/orm');
        static::assertEquals('point3d', $type);
    }

    /**
     * Test to store a point and find it by its geometric.
     */
    public function testFindByPoint(): void
    {
        $point = new Point3D(1, 2, 3);
        $entity = new Point3DEntity();

        $entity->setPoint($point);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $result = $this->getEntityManager()->getRepository(self::POINT3D_ENTITY)->findByPoint($point);

        static::assertEquals($entity, $result[0]);
    }

    /**
     * Unit test the getName, getSQLType and getBindingType methods.
     *
     * @throws TypeNotRegistered It shall not happen
     */
    public function testName(): void
    {
        if (!Type::hasType('point3d')) {
            Type::addType('point3d', Point3DType::class);
        }

        $spatialInstance = new Point3DType();
        static::assertNotFalse($spatialInstance->getName());
        static::assertSame('point3d', $spatialInstance->getName());
        static::assertSame(ParameterType::STRING, $spatialInstance->getBindingType());
        static::assertSame('POINTZ', $spatialInstance->getSQLType());
    }

    /**
     * Test to store a null point and find it by its id.
     */
    public function testNullPoint(): void
    {
        $pointEntity = new Point3DEntity();
        $this->getEntityManager()->persist($pointEntity);
        $this->getEntityManager()->flush();
        static::assertIsRetrievableById($this->getEntityManager(), $pointEntity);
    }

    /**
     * Test to store a point and find it by its id.
     */
    public function testPoint(): void
    {
        $point = new Point3D(1, 2, 3);
        $pointEntity = new Point3DEntity();
        $pointEntity->setPoint($point);
        $this->getEntityManager()->persist($pointEntity);
        $this->getEntityManager()->flush();

        static::assertIsRetrievableById($this->getEntityManager(), $pointEntity);
    }
}
