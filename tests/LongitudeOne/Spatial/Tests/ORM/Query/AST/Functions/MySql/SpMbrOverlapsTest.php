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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\MySql;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use LongitudeOne\Spatial\Tests\Helper\PolygonHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;

/**
 * MySQL_MbrOverlaps DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 * @group mysql-only
 *
 * @internal
 *
 * @coversDefaultClass
 */
class SpMbrOverlapsTest extends OrmTestCase
{
    use PolygonHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->supportsPlatform(MySQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testFunctionInPredicate(): void
    {
        $bigPolygon = $this->persistBigPolygon();
        $this->persistSmallPolygon();
        $this->persistHoleyPolygon();
        $polygonW = $this->persistPolygonW();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT p FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p WHERE MySQL_MbrOverlaps(p.polygon, ST_GeomFromText(:p)) = true'
            // phpcs:enable
        );
        $query->setParameter('p', 'POLYGON((4 4, 4 12, 12 12, 12 4, 4 4))', 'string');
        $result = $query->getResult();

        static::assertCount(3, $result);
        static::assertEquals($bigPolygon, $result[0]);
        static::assertEquals($polygonW, $result[2]);
    }

    /**
     * Test a DQL containing function to test.
     *
     * @group geometry
     */
    public function testFunctionInSelect(): void
    {
        $bigPolyon = $this->persistBigPolygon();
        $smallPolygon = $this->persistSmallPolygon();
        $polygonW = $this->persistPolygonW();
        $holeyPolygon = $this->persistHoleyPolygon();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT p, MySQL_MbrOverlaps(p.polygon, ST_GeomFromText(:p)) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p'
            // phpcs:enable
        );
        $query->setParameter('p', 'POLYGON((0 0, 0 12, 12 12, 12 0, 0 0))', 'string');
        $result = $query->getResult();

        static::assertCount(4, $result);
        static::assertEquals($bigPolyon, $result[0][0]);
        static::assertEquals(0, $result[0][1]);
        static::assertEquals($smallPolygon, $result[1][0]);
        static::assertEquals(0, $result[1][1]);
        static::assertEquals($polygonW, $result[2][0]);
        static::assertEquals(1, $result[2][1]);
        static::assertEquals($holeyPolygon, $result[3][0]);
        static::assertEquals(0, $result[3][1]);
    }
}
