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
use LongitudeOne\Spatial\Tests\Helper\PersistantLineStringHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_LineCrossingDirection DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @internal
 *
 * @coversDefaultClass
 */
#[Group('dql')]
#[Group('pgsql-only')]
class SpLineCrossingDirectionTest extends PersistOrmTestCase
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
     */
    #[Group('geometry')]
    public function testInPredicate(): void
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

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertEquals($lineStringY, $result[0]);
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testInSelect(): void
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

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertEquals($lineStringX, $result[0][0]);
        static::assertEquals(2, $result[0][1]);
        static::assertEquals($lineStringY, $result[1][0]);
        static::assertEquals(1, $result[1][1]);
        static::assertEquals($lineStringZ, $result[2][0]);
        static::assertEquals(-1, $result[2][1]);
    }
}
