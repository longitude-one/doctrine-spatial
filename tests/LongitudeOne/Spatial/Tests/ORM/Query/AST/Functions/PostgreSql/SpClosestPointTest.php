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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\PostgreSql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\PostgreSql\SpClosestPoint;
use LongitudeOne\Spatial\Tests\Helper\PersistantLineStringHelperTrait;
use LongitudeOne\Spatial\Tests\Helper\PersistantPolygonHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * SP_ClosestPoint DQL function tests.
 * This function is not issue from the OGC, but it is useful for Database postgresql.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(SpClosestPoint::class)]
#[Group('dql')]
#[Group('pgsql-only')]
class SpClosestPointTest extends PersistOrmTestCase
{
    use PersistantLineStringHelperTrait;
    use PersistantPolygonHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testFunctionInSelect(): void
    {
        $straight = $this->persistStraightLineString();
        $lineC = $this->persistLineStringC();
        $ring = $this->persistRingLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l, ST_AsText(PgSql_ClosestPoint(l.lineString, ST_GeomFromText(:p))) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
        );
        $query->setParameter('p', 'POINT(4 3)');
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertIsArray($result[0]);
        static::assertIsArray($result[1]);
        static::assertIsArray($result[2]);
        static::assertEquals($straight, $result[0][0]);
        static::assertSame('POINT(3.5 3.5)', $result[0][1]);
        static::assertEquals($lineC, $result[1][0]);
        static::assertSame('POINT(4.5 2.5)', $result[1][1]);
        static::assertEquals($ring, $result[2][0]);
        static::assertSame('POINT(1 1)', $result[2][1]);
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testFunctionWithPolygonInSelect(): void
    {
        $bigPolygon = $this->persistBigPolygon();
        $smallPolygon = $this->persistSmallPolygon();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p, ST_AsText(PgSql_ClosestPoint(p.polygon, ST_GeomFromText(:p1))) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p'
        );

        $query->setParameter('p1', 'POINT(2 2)', 'string');

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(2, $result);
        static::assertIsArray($result[0]);
        static::assertIsArray($result[1]);
        static::assertEquals($bigPolygon, $result[0][0]);
        static::assertEquals('POINT(2 2)', $result[0][1]);
        static::assertEquals($smallPolygon, $result[1][0]);
        static::assertEquals('POINT(5 5)', $result[1][1]);
    }
}
