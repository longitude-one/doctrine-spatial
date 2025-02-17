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
 * ST_LineInterpolatePoint DQL function tests.
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
class SpLineInterpolatePointTest extends PersistOrmTestCase
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
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testInSelect(): void
    {
        $lineStringX = $this->persistLineStringX();
        $lineStringY = $this->persistLineStringY();
        $straightLineString = $this->persistStraightLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l, ST_AsText(PgSql_LineInterpolatePoint(l.lineString, :p)) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
        );

        $query->setParameter('p', 0.2);

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertEquals($lineStringX, $result[0][0]);
        static::assertEquals('POINT(7.2 13.6)', $result[0][1]);
        static::assertEquals($lineStringY, $result[1][0]);
        static::assertEquals('POINT(10.2 12)', $result[1][1]);
        static::assertEquals($straightLineString, $result[2][0]);
        static::assertEquals('POINT(1 1)', $result[2][1]);
    }
}
