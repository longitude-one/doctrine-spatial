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
use LongitudeOne\Spatial\Tests\Helper\PointHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;

/**
 * ST_SnapToGrid DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group dql
 * @group pgsql-only
 *
 * @internal
 *
 * @coversDefaultClass
 */
class SpSnapToGridTest extends OrmTestCase
{
    use PointHelperTrait;

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
     * Test a DQL containing function with 2 parameters to test in the select.
     *
     * @group geometry
     */
    public function testSelectStSnapToGridSignature2Parameters()
    {
        $this->persistGeometryPoint('in grid', '1.25', '2.55');

        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_AsText(PgSql_SnapToGrid(p.point, 0.5)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        );
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(1 2.5)'],
        ];

        static::assertEquals($expected, $result);
    }

    /**
     * Test a DQL containing function with three parameters to test in the select.
     *
     * @group geometry
     */
    public function testSelectStSnapToGridSignature3Parameters()
    {
        $this->persistGeometryPoint('in grid', '1.25', '2.55');

        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_AsText(PgSql_SnapToGrid(p.point, 0.5, 1)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        );
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(1 3)'],
        ];

        static::assertEquals($expected, $result);
    }

    /**
     * Test a DQL containing function with five parameters to test in the select.
     *
     * @group geometry
     */
    public function testSelectStSnapToGridSignature5Parameters()
    {
        $this->persistGeometryPoint('in grid', '5.25', '6.55');

        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_AsText(PgSql_SnapToGrid(p.point, 5.55, 6.25, 0.5, 0.5)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        );
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(5.05 6.75)'],
        ];

        static::assertEquals($expected, $result);
    }

    /**
     * Test a DQL containing function with six paramters to test in the select.
     *
     * @group geometry
     */
    public function testSelectStSnapToGridSignature6Parameters()
    {
        $this->persistGeometryPoint('in grid', '5.25', '6.55');

        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_AsText(PgSql_SnapToGrid(p.point, p.point, 0.005, 0.025, 0.5, 0.01)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        );
        $result = $query->getResult();

        $expected = [
            [1 => 'POINT(5.25 6.55)'],
        ];

        static::assertEquals($expected, $result);
    }
}
