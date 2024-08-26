<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
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

use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\Tests\Helper\PersistantLineStringHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * ST_SymDifference DQL function tests.
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
class StSymDifferenceTest extends PersistOrmTestCase
{
    use PersistantLineStringHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        $this->supportsPlatform(MariaDBPlatform::class);
        $this->supportsPlatform(MySQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testSelectStDifference(): void
    {
        $lineStringA = $this->persistLineStringA();
        $lineStringB = $this->persistLineStringB();
        $lineStringC = $this->persistLineStringC();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l, ST_AsText(ST_SymDifference(ST_GeomFromText(:p), l.lineString)) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
        );

        $query->setParameter('p', 'LINESTRING(0 0, 12 12)', 'string');

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertEquals($lineStringA, $result[0][0]);
        static::assertEquals('LINESTRING(10 10,12 12)', $result[0][1]);
        static::assertEquals($lineStringB, $result[1][0]);

        // MySQL failed ST_SymDifference implementation. A linestring minus another one should cut the line.
        // The result SHALL be a multilinestring with two lineStrings.
        $expected = 'MULTILINESTRING((0 0,6 6),(0 10,6 6),(6 6,12 12),(6 6,15 0))';
        if ($this->getPlatform() instanceof MySQLPlatform) {
            $expected = 'MULTILINESTRING((0 0,12 12),(0 10,15 0))';
        } elseif ($this->getPlatform() instanceof MariaDBPlatform) {
            $expected = 'MULTILINESTRING((0 0,6 6),(15 0,6 6),(6 6,0 10),(6 6,12 12))';
        }

        static::assertIsArray($result);
        static::assertEquals($expected, $result[1][1]);
        static::assertEquals($lineStringC, $result[2][0]);
        static::assertEquals('MULTILINESTRING((0 0,12 12),(2 0,12 10))', $result[2][1]);
    }

    /**
     * Test a DQL containing function to test in the predicate.
     *
     * @group geometry
     */
    public function testStDifferenceWhereParameter(): void
    {
        $lineStringA = $this->persistLineStringA();
        $lineStringB = $this->persistLineStringB();
        $lineStringC = $this->persistLineStringC();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l WHERE ST_IsEmpty(ST_SymDifference(ST_GeomFromText(:p1), l.lineString)) = false'
        );

        $query->setParameter('p1', 'LINESTRING(0 0, 10 10)', 'string');

        $result = $query->getResult();

        static::assertIsArray($result);
        if ($this->getPlatform() instanceof MariaDBPlatform) {
            static::assertCount(3, $result);
            static::assertEquals($lineStringA, $result[0]);
            static::assertEquals($lineStringB, $result[1]);
            static::assertEquals($lineStringC, $result[2]);

            return;
        }

        static::assertCount(2, $result);
        static::assertEquals($lineStringB, $result[0]);
        static::assertEquals($lineStringC, $result[1]);
    }
}
