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
use LongitudeOne\Spatial\ORM\Query\AST\Functions\PostgreSql\SpSnapToGrid;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_SnapToGrid DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(SpSnapToGrid::class)]
#[Group('dql')]
#[Group('pgsql-only')]
class SpSnapToGridTest extends PersistOrmTestCase
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
     * Test a DQL containing function with 2 parameters to test in the select.
     */
    #[Group('geometry')]
    public function testSelectStSnapToGridSignature2Parameters(): void
    {
        $this->createAndPersistGeometricPoint('in grid', '1.25', '2.55');

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
     */
    #[Group('geometry')]
    public function testSelectStSnapToGridSignature3Parameters(): void
    {
        $this->createAndPersistGeometricPoint('in grid', '1.25', '2.55');

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
     */
    #[Group('geometry')]
    public function testSelectStSnapToGridSignature5Parameters(): void
    {
        $this->createAndPersistGeometricPoint('in grid', '5.25', '6.55');

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
     * Test a DQL containing function with six parameters to test in the select.
     */
    #[Group('geometry')]
    public function testSelectStSnapToGridSignature6Parameters(): void
    {
        $this->createAndPersistGeometricPoint('in grid', '5.25', '6.55');

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
