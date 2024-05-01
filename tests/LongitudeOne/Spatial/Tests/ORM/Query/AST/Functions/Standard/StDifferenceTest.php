<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
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
use LongitudeOne\Spatial\Tests\Helper\LineStringHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;

/**
 * ST_Difference DQL function tests.
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
class StDifferenceTest extends OrmTestCase
{
    use LineStringHelperTrait;

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
    public function testSelectStDifference()
    {
        $lineStringA = $this->persistLineStringA();
        $lineStringB = $this->persistLineStringB();
        $lineStringC = $this->persistLineStringC();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT l, ST_AsText(ST_Difference(ST_GeomFromText(:p), l.lineString)) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
            // phpcs:enable
        );

        $query->setParameter('p', 'LINESTRING(0 0, 12 12)', 'string');

        $result = $query->getResult();

        static::assertCount(3, $result);
        static::assertEquals($lineStringA, $result[0][0]);
        static::assertEquals('LINESTRING(10 10,12 12)', $result[0][1]);
        static::assertEquals($lineStringB, $result[1][0]);
        // Here is the only good result one.
        // A linestring minus another crossing linestring returns initial linestring split
        $expected = 'MULTILINESTRING((0 0,6 6),(6 6,12 12))';
        if ($this->getPlatform() instanceof MySQLPlatform) {
            // MySQL failed ST_Difference implementation, so I test the bad result.
            $expected = 'LINESTRING(0 0,12 12)';
        }
        static::assertEquals($expected, $result[1][1]);
        static::assertEquals($lineStringC, $result[2][0]);
        static::assertEquals('LINESTRING(0 0,12 12)', $result[2][1]);
    }

    /**
     * Test a DQL containing function to test in the predicate.
     *
     * @group geometry
     */
    public function testStDifferenceWhereParameter()
    {
        $this->persistLineStringA();
        $lineStringB = $this->persistLineStringB();
        $lineStringC = $this->persistLineStringC();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT l FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l WHERE ST_IsEmpty(ST_Difference(ST_GeomFromText(:p1), l.lineString)) = false'
            // phpcs:enable
        );

        $query->setParameter('p1', 'LINESTRING(0 0, 10 10)', 'string');

        $result = $query->getResult();

        static::assertCount(2, $result);
        static::assertEquals($lineStringB, $result[0]);
        static::assertEquals($lineStringC, $result[1]);
    }
}
