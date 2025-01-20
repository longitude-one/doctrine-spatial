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
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * SP_Simplify DQL function tests.
 * This function is not issue from the OGC, but it is useful for Database postgresql.
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
class SpSimplifyTest extends PersistOrmTestCase
{
    use PersistantPointHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testFunctionInSelect(): void
    {
        $pointO = $this->persistPointO();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p, PgSQL_NPoints(PgSQL_Simplify(ST_Buffer(p.point, 10, 12), 10)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        );
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertEquals($pointO, $result[0][0]);
        static::assertEquals(4, $result[0][1]);
    }
}
