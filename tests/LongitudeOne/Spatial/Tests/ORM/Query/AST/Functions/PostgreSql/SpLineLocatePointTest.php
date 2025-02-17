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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\PostgreSql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\Tests\Helper\PersistantLineStringHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * ST_LineLocatePoint DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 * @group pgsql-only
 *
 * @internal
 *
 * @coversDefaultClass
 */
class SpLineLocatePointTest extends PersistOrmTestCase
{
    use PersistantLineStringHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the predicate.
     *
     * @group geometry
     */
    public function testPredicate(): void
    {
        $this->persistStraightLineString();
        $lineA = $this->persistLineStringA();
        $lineB = $this->persistLineStringB();

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l WHERE PgSql_LineLocatePoint(l.lineString, ST_GeomFromText(:point)) < :percent'
        );

        $query->setParameter('point', 'POINT(4 3)', 'string');
        $query->setParameter('percent', 0.5);

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(2, $result);
        static::assertEquals($lineA, $result[0]);
        static::assertEquals($lineB, $result[1]);
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testSelect(): void
    {
        $this->persistStraightLineString();
        $this->persistLineStringA();
        $this->persistLineStringB();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT PgSql_LineLocatePoint(l.lineString, :point) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
        );
        $query->setParameter('point', 'POINT(4 3)');
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertEquals(0.7, $result[0][1]);
        static::assertEquals(0.35, $result[1][1]);
        static::assertEqualsWithDelta(0.4, $result[2][1], 0.000000000001);
    }
}
