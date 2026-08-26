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
use LongitudeOne\Spatial\ORM\Query\AST\Functions\PostgreSql\SpScale;
use LongitudeOne\Spatial\Tests\Helper\PersistantLineStringHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * SP_Scale DQL function tests.
 * This function is not issue from the OGC, but it is useful for Database postgresql.
 *
 * @see https://postgis.net/docs/ST_Scale.html
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(SpScale::class)]
#[Group('dql')]
#[Group('pgsql-only')]
class SpScaleTest extends PersistOrmTestCase
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
     */
    #[Group('geometry')]
    public function testFunctionInSelect(): void
    {
        $straightLineString = $this->persistStraightLineString();
        $angularLineString = $this->persistAngularLineString();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l, ST_AsText(PgSQL_Scale(l.lineString, :x, :y)) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
        );
        $query->setParameter('x', 2);
        $query->setParameter('y', 4);
        // TODO Try to solve this issue on Travis Linux
        // SQLSTATE[XX000]: Internal error: 7 ERROR:  parse error - invalid geometry
        // HINT:  "2" <-- parse error at position 2 within geometry
        static::markTestSkipped('On Linux env only, Postgis throw an internal error');
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(2, $result);
        static::assertIsArray($result[0]);
        static::assertIsArray($result[1]);
        static::assertEquals($straightLineString, $result[0][0]);
        static::assertSame('LINESTRING(0 0,4 8,10 20)', $result[0][1]);
        static::assertEquals($angularLineString, $result[1][0]);
        static::assertEquals('LINESTRING(6 12,8 60,10 88)', $result[1][1]);
    }
}
