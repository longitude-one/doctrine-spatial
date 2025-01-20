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
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Exception\TypeNotRegistered;
use Doctrine\DBAL\Types\Type;
use LongitudeOne\Spatial\DBAL\Types\Geometry\LineStringType;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * Doctrine LineStringType tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group geometry
 *
 * @internal
 *
 * @coversDefaultClass \LongitudeOne\Spatial\DBAL\Types\Geometry\LineStringType
 */
class LineStringTypeTest extends PersistOrmTestCase
{
    /**
     * Set up the test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->supportsPlatform(MySQLPlatform::class);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        parent::setUp();
    }

    /**
     * Test to store and find a line string in table.
     *
     * @throws InvalidValueException when geometries are not valid
     */
    public function testFindByLineString(): void
    {
        $lineString = new LineString(
            [
                new Point('0', '0'),
                new Point('1', '1'),
                new Point('2', '2'),
            ]
        );
        $entity = new LineStringEntity();

        $entity->setLineString($lineString);
        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }

    /**
     * Test to store and find it by id.
     *
     * @throws InvalidValueException when geometries are not valid
     */
    public function testLineString(): void
    {
        $lineString = new LineString(
            [
                new Point('0', '0'),
                new Point('1', '1'),
                new Point('2', '2'),
            ]
        );
        $entity = new LineStringEntity();

        $entity->setLineString($lineString);
        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }

    /**
     * Unit test the getName, getSQLType and getBindingType methods.
     *
     * @throws TypeNotRegistered It shall not happen
     */
    public function testName(): void
    {
        if (!Type::hasType('linestring')) {
            Type::addType('linestring', LineStringType::class);
        }

        $spatialInstance = new LineStringType();
        static::assertNotFalse($spatialInstance->getName());
        static::assertSame('linestring', $spatialInstance->getName());
        static::assertSame(ParameterType::STRING, $spatialInstance->getBindingType());
        static::assertSame('LineString', $spatialInstance->getSQLType());
    }

    /**
     * Test to store a null line string, then to find it with its id.
     */
    public function testNullLineStringType(): void
    {
        $entity = new LineStringEntity();
        static::assertIsRetrievableById($this->getEntityManager(), $entity);
    }

    // TODO test to find all null linestring
}
