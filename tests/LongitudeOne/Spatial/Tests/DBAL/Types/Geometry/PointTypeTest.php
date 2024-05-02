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

namespace LongitudeOne\Spatial\Tests\DBAL\Types\Geometry;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\Tests\Fixtures\PointEntity;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;

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
class PointTypeTest extends OrmTestCase
{
    use PersistantPointHelperTrait;

    /**
     * Set up the test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform(MySQLPlatform::class);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        parent::setUp();
    }

    // TODO test to find a null geometry

    /**
     * Test the declaration type.
     */
    public function testDeclarationType(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $metadata = $this->getEntityManager()->getClassMetadata(PointEntity::class);

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
        static::assertEquals('point', $type);
    }

    /**
     * Test to store a point and find it by its geometric.
     */
    public function testFindByPoint(): void
    {
        $point = static::createPointA();
        $entity = new PointEntity();

        $entity->setPoint($point);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        $result = $this->getEntityManager()->getRepository(self::POINT_ENTITY)->findByPoint($point);

        static::assertEquals($entity, $result[0]);
    }

    /**
     * Test to store a null point and find it by its id.
     */
    public function testNullPoint(): void
    {
        $entity = new PointEntity();
        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }

    /**
     * Test to store a point and find it by its id.
     */
    public function testPoint(): void
    {
        $entity = $this->persistPointA();
        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }
}
