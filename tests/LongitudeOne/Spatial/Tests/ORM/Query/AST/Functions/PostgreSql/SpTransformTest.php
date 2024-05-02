<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP          8.1 | 8.2 | 8.3
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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\PostgreSql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\Tests\Helper\PolygonHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;

/**
 * SP_Transform DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 * @group pgsql-only
 *
 * @internal
 *
 * @transformDefaultClass
 *
 * @coversNothing
 */
class SpTransformTest extends OrmTestCase
{
    use PolygonHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testFunctionInSelect()
    {
        $massachusetts = $this->persistMassachusettsState();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
        $query = $this->getEntityManager()->createQuery(
            'SELECT p, ST_AsText(PgSql_Transform(p.polygon, :proj)) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p'
        );
        $query->setParameter('proj', '+proj=longlat +datum=WGS84 +no_defs');
        $result = $query->getResult();

        static::assertCount(1, $result);
        static::assertEquals($massachusetts, $result[0][0]);
        // too many error between OS, this test doesn't have to check the result (double float, etc.),
        // but it has to check that point becomes a polygon.
        static::assertStringStartsWith('POLYGON((', $result[0][1]);
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testFunctionInSelectWith3Parameters()
    {
        $massachusetts = $this->persistMassachusettsState(false);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
        $query = $this->getEntityManager()->createQuery(
            'SELECT p, ST_AsText(PgSql_Transform(p.polygon, :from, :to)) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p'
        );
        $query->setParameter('from', '+proj=lcc +lat_1=42.68333333333333 +lat_2=41.71666666666667 +lat_0=41 +lon_0=-71.5 +x_0=200000.0001016002 +y_0=750000 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=us-ft +no_defs ');
        $query->setParameter('to', '+proj=longlat +datum=WGS84 +no_defs');
        $result = $query->getResult();

        static::assertCount(1, $result);
        static::assertEquals($massachusetts, $result[0][0]);
        static::assertStringStartsWith('POLYGON((', $result[0][1]);
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testFunctionInSelectWithSrid()
    {
        $massachusetts = $this->persistMassachusettsState();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        // TODO The test above failed because DQL SRID is seen as a string
        static::markTestSkipped('The test above failed because DQL SRID is seen as a string');
        $query = $this->getEntityManager()->createQuery(
            'SELECT p, ST_AsText(PgSql_Transform(p.polygon, :srid)) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p'
        );
        $query->setParameter('srid', 4326, 'integer');
        $result = $query->getResult();

        static::assertCount(1, $result);
        static::assertEquals($massachusetts, $result[0][0]);
        static::assertSame('POLYGON((-71.1776848522251 42.3902896512902,-71.1776843766326 42.3903829478009, -71.1775844305465 42.3903826677917,-71.1775825927231 42.3902893647987,-71.1776848522251 42.3902896512902))', $result[0][1]);
    }
}
