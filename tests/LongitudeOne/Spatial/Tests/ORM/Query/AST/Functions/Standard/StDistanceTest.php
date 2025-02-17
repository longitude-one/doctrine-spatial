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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\Standard;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * ST_Distance DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 *
 * @coversDefaultClass
 */
class StDistanceTest extends PersistOrmTestCase
{
    use PersistantPointHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->usesEntity(self::GEOGRAPHY_ENTITY);
        $this->usesType('geopoint');
        $this->supportsPlatform(PostgreSQLPlatform::class);
        // TODO Check if MySSQL doesn't support this function or if I missed this function

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geography
     */
    public function testSelectStDistanceGeographyCartesian(): void
    {
        $newYork = $this->persistNewYorkGeography();
        $losAngeles = $this->persistLosAngelesGeography();
        $dallas = $this->persistDallasGeography();

        $query = $this->getEntityManager()->createQuery(
            'SELECT g, ST_Distance(g.geography, PgSql_GeographyFromText(:p1)) FROM LongitudeOne\Spatial\Tests\Fixtures\GeographyEntity g'
        );

        $query->setParameter('p1', 'POINT(-89.4 43.066667)', 'string');

        $result = $query->getResult();

        // TODO: Test should be fixed, distance are differents on Windows and on Linux.
        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertEquals($newYork, $result[0][0]);
        static::assertGreaterThan(1309000, $result[0][1]);
        static::assertLessThan(1310000, $result[0][1]);
        static::assertEquals($losAngeles, $result[1][0]);
        static::assertGreaterThan(2680000, $result[1][1]);
        static::assertLessThan(2690000, $result[1][1]);
        static::assertEquals($dallas, $result[2][0]);
        static::assertGreaterThan(1310000, $result[2][1]);
        static::assertLessThan(1320000, $result[2][1]);
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geography
     */
    public function testSelectStDistanceGeographySpheroid(): void
    {
        $newYork = $this->persistNewYorkGeography();
        $losAngeles = $this->persistLosAngelesGeography();
        $dallas = $this->persistDallasGeography();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        // TODO This test should be moved to a class implementing only PgSQL
        $query = $this->getEntityManager()->createQuery(
            'SELECT g, ST_Distance(g.geography, PgSql_GeographyFromText(:p1)) FROM LongitudeOne\Spatial\Tests\Fixtures\GeographyEntity g'
        );

        $query->setParameter('p1', 'POINT(-89.4 43.066667)', 'string');

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertEquals($newYork, $result[0][0]);
        static::assertEquals(1309106.31458423, $result[0][1]);
        static::assertEquals($losAngeles, $result[1][0]);
        static::assertEquals(2689041.41288843, $result[1][1]);
        static::assertEquals($dallas, $result[2][0]);
        static::assertEquals(1312731.61417061, $result[2][1]);
    }

    /**
     * Test a DQL containing function to test.
     *
     * @group geometry
     */
    public function testSelectStDistanceGeometryCartesian(): void
    {
        $newYork = $this->persistNewYorkGeometry();
        $losAngeles = $this->persistLosAngelesGeometry();
        $dallas = $this->persistDallasGeometry();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p, ST_Distance(p.point, ST_GeomFromText(:p1)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        );

        $query->setParameter('p1', 'POINT(-89.4 43.066667)', 'string');

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertEquals($newYork, $result[0][0]);
        static::assertEqualsWithDelta(15.646934398128, $result[0][1], 0.000000000001);
        static::assertEquals($losAngeles, $result[1][0]);
        static::assertEqualsWithDelta(30.2188561049899, $result[1][1], 0.000000000001);
        static::assertEquals($dallas, $result[2][0]);
        static::assertEqualsWithDelta(12.6718564262953, $result[2][1], 0.000000000001);
    }
}
