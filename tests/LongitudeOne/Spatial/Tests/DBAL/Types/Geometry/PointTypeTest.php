<?php
/**
 * This file is part of the Doctrine Spatial extension.
 *
 * PHP 8.4 | 8.5
 * Doctrine ORM ^3.6
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2026
 * Copyright Longitude One 2020-2026
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
use LongitudeOne\Spatial\DBAL\Types\Geometry\PointType;
use LongitudeOne\Spatial\Tests\Fixtures\PointEntity;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Doctrine PointType tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(PointType::class)]
#[Group('geometry')]
class PointTypeTest extends PersistOrmTestCase
{
    use PersistantPointHelperTrait;

    /**
     * Set up the test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform(MariaDBPlatform::class);
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

        // doctrine/orm:3.1, doctrine/orm:4.0
        $type = $metadata->getFieldMapping('point')->type;

        // Check the type
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
     * Unit test the getName, getSQLType and getBindingType methods.
     *
     * @throws TypeNotRegistered It shall not happen
     */
    public function testName(): void
    {
        if (!Type::hasType('point')) {
            Type::addType('point', PointType::class);
        }

        $spatialInstance = new PointType();
        static::assertSame('point', $spatialInstance->getName());
        static::assertSame(ParameterType::STRING, $spatialInstance->getBindingType());
        static::assertSame('Point', $spatialInstance->getSQLType());
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
