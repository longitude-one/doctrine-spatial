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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\Standard;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\Tests\Helper\LineStringHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;

/**
 * ST_Relates DQL function tests.
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
class StRelateTest extends OrmTestCase
{
    use LineStringHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        // TODO Check if MySSQL doesn't support this function or if I missed this function

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testFunctionInPredicate()
    {
        $linestring = $this->persistStraightLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            "SELECT l FROM LongitudeOne\\Spatial\\Tests\\Fixtures\\LineStringEntity l WHERE ST_Relate(l.lineString, ST_GeomFromText(:p)) = 'FF1FF0102'"
            // phpcs:enable
        );
        $query->setParameter('p', 'LINESTRING(6 6, 8 8, 11 11)', 'string');
        $result = $query->getResult();

        static::assertCount(1, $result);
        static::assertEquals($linestring, $result[0]);
    }

    /**
     * Test a DQL containing function to test.
     *
     * @group geometry
     */
    public function testFunctionInSelect()
    {
        $straightLineString = $this->persistStraightLineString();
        $angularLineString = $this->persistAngularLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT l, ST_Relate(l.lineString, ST_GeomFromText(:p)) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
            // phpcs:enable
        );
        $query->setParameter('p', 'LINESTRING(6 6, 8 8, 11 11)', 'string');
        $result = $query->getResult();

        static::assertCount(2, $result);
        static::assertEquals($straightLineString, $result[0][0]);
        static::assertEquals('FF1FF0102', $result[0][1]);
        static::assertEquals($angularLineString, $result[1][0]);
        static::assertEquals('FF1FF0102', $result[1][1]);
    }
}
