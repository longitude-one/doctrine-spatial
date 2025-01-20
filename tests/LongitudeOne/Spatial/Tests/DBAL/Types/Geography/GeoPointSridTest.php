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

namespace LongitudeOne\Spatial\Tests\DBAL\Types\Geography;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Exception\TypeNotRegistered;
use Doctrine\DBAL\Types\Type;
use LongitudeOne\Spatial\DBAL\Types\Geography\PointType;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geography\Point;
use LongitudeOne\Spatial\Tests\Fixtures\GeoPointSridEntity;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * Doctrine GeographyType tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group srid
 *
 * @internal
 *
 * @coversDefaultClass \LongitudeOne\Spatial\DBAL\Types\Geography\PointType
 */
class GeoPointSridTest extends PersistOrmTestCase
{
    /**
     * Set up the test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::GEO_POINT_SRID_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        parent::setUp();
    }

    /**
     * Test to persist a geographic point then find it by its geography.
     */
    public function testFindGeographyBy(): void
    {
        try {
            $point = new Point(11, 11);
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to create a geography point (11 11): %s', $e->getMessage()));
        }
        $entity = new GeoPointSridEntity();
        $entity->setPoint($point);

        /** @var GeoPointSridEntity[] $queryEntities */
        $queryEntities = static::assertIsRetrievableByGeo($this->getEntityManager(), $entity, $point, 'findByPoint');
        static::assertEquals(4326, $queryEntities[0]->getPoint()->getSrid());
    }

    /**
     * Unit test the getName, getSQLType and getBindingType methods.
     *
     * @throws TypeNotRegistered It shall not happen
     */
    public function testName(): void
    {
        if (!Type::hasType('geopoint')) {
            Type::addType('geopoint', PointType::class);
        }

        static::assertTrue(Type::hasType('geopoint'));
        $spatialInstance = new PointType();
        static::assertNotFalse($spatialInstance->getName());
        static::assertSame('geopoint', $spatialInstance->getName());
        static::assertSame(ParameterType::STRING, $spatialInstance->getBindingType());
        static::assertSame('Point', $spatialInstance->getSQLType());
    }

    /**
     * Test a null geography.
     */
    public function testNullGeography(): void
    {
        $entity = new GeoPointSridEntity();
        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }

    /**
     * Test to persist a geographic point then find it by its id.
     */
    public function testPointGeographyById(): void
    {
        $entity = new GeoPointSridEntity();

        try {
            $entity->setPoint(new Point(11, 11));
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to create a point (11 11): %s', $e->getMessage()));
        }

        /** @var GeoPointSridEntity $queryEntity */
        $queryEntity = static::assertIsRetrievableById($this->getEntityManager(), $entity);
        static::assertEquals(4326, $queryEntity->getPoint()->getSrid());
    }
}
