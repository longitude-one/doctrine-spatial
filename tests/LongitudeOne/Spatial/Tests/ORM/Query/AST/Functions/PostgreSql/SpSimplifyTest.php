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
use LongitudeOne\Spatial\ORM\Query\AST\Functions\PostgreSql\SpSimplify;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * SP_Simplify DQL function tests.
 * This function is not issue from the OGC, but it is useful for Database postgresql.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(SpSimplify::class)]
#[Group('dql')]
#[Group('pgsql-only')]
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
     */
    #[Group('geometry')]
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
        static::assertIsArray($result[0]);
        static::assertEquals($pointO, $result[0][0]);
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testFunctionInSelectWithThreeParameters(): void
    {
        $pointO = $this->persistPointO();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p, PgSQL_NPoints(PgSQL_Simplify(ST_Buffer(p.point, 10, 12), 10, true)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        );
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertIsArray($result[0]);
        static::assertEquals($pointO, $result[0][0]);
        static::assertNotNull($result[0][1]);
    }
}
