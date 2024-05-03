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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\Standard;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\Tests\Helper\PersistantLineStringHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * ST_NumPoints DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 *
 * @coversDefaultClass
 */
class StNumPointsTest extends PersistOrmTestCase
{
    use PersistantLineStringHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        $this->supportsPlatform(MySQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testFunction(): void
    {
        $straightLineString = $this->persistStraightLineString();
        $angularLineString = $this->persistAngularLineString();
        $ringLineString = $this->persistRingLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l, ST_NumPoints(l.lineString) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
        );
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertEquals($straightLineString, $result[0][0]);
        static::assertEquals(3, $result[0][1]);
        static::assertEquals($angularLineString, $result[1][0]);
        static::assertEquals(3, $result[1][1]);
        static::assertEquals($ringLineString, $result[2][0]);
        static::assertEquals(5, $result[2][1]);
    }

    /**
     * Test a DQL containing function to test in the predicate.
     *
     * @group geometry
     */
    public function testFunctionInPredicate(): void
    {
        $this->persistStraightLineString();
        $this->persistAngularLineString();
        $ringLineString = $this->persistRingLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l where  ST_NumPoints(l.lineString) = :p'
        );
        $query->setParameter('p', 5, 'integer');
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertEquals($ringLineString, $result[0]);
    }
}
