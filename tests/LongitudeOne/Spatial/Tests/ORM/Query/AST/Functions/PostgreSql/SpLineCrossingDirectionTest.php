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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\PostgreSql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\Tests\Helper\LineStringHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;

/**
 * ST_LineCrossingDirection DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group dql
 * @group pgsql-only
 *
 * @internal
 *
 * @coversDefaultClass
 */
class SpLineCrossingDirectionTest extends OrmTestCase
{
    use LineStringHelperTrait;

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
    public function testInPredicate()
    {
        $this->persistLineStringX();
        $lineStringY = $this->persistLineStringY();
        $this->persistLineStringZ();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l WHERE PgSql_LineCrossingDirection(l.lineString, ST_GeomFromText(:p1)) = 1'
        );

        $query->setParameter('p1', 'LINESTRING(12 6,5 11,8 12,5 15)', 'string');

        $result = $query->getResult();

        static::assertCount(1, $result);
        static::assertEquals($lineStringY, $result[0]);
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testInSelect()
    {
        $lineStringX = $this->persistLineStringX();
        $lineStringY = $this->persistLineStringY();
        $lineStringZ = $this->persistLineStringZ();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l, PgSql_LineCrossingDirection(l.lineString, ST_GeomFromText(:p1)) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
        );

        $query->setParameter('p1', 'LINESTRING(12 6,5 11,8 12,5 15)', 'string');

        $result = $query->getResult();

        static::assertCount(3, $result);
        static::assertEquals($lineStringX, $result[0][0]);
        static::assertEquals(2, $result[0][1]);
        static::assertEquals($lineStringY, $result[1][0]);
        static::assertEquals(1, $result[1][1]);
        static::assertEquals($lineStringZ, $result[2][0]);
        static::assertEquals(-1, $result[2][1]);
    }
}
