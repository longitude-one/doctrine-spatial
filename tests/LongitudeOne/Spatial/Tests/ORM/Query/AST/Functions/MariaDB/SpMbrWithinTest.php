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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\MariaDB;

use Doctrine\DBAL\Platforms\MariaDBPlatform;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\MariaDB\SpMbrWithin;
use LongitudeOne\Spatial\Tests\Helper\PersistantPolygonHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * MariaDB_MbrWithin DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(SpMbrWithin::class)]
#[Group('dql')]
#[Group('mariadb-only')]
class SpMbrWithinTest extends PersistOrmTestCase
{
    use PersistantPolygonHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->supportsPlatform(MariaDBPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testFunctionInPredicate(): void
    {
        $this->persistBigPolygon();
        $smallPolygon = $this->persistSmallPolygon();
        $this->persistHoleyPolygon();
        $this->persistPolygonW();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p WHERE MariaDB_MbrWithin(p.polygon, ST_GeomFromText(:p)) = true'
        );
        $query->setParameter('p', 'POLYGON((4 4, 4 12, 12 12, 12 4, 4 4))', 'string');
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertEquals($smallPolygon, $result[0]);
    }

    /**
     * Test a DQL containing function to test.
     */
    #[Group('geometry')]
    public function testFunctionInSelect(): void
    {
        $bigPolygon = $this->persistBigPolygon();
        $smallPolygon = $this->persistSmallPolygon();
        $polygonW = $this->persistPolygonW();
        $holeyPolygon = $this->persistHoleyPolygon();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p, MariaDB_MbrWithin(p.polygon, ST_GeomFromText(:p)) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p'
        );
        $query->setParameter('p', 'POLYGON((0 0, 0 12, 12 12, 12 0, 0 0))', 'string');
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(4, $result);
        static::assertIsArray($result[0]);
        static::assertIsArray($result[1]);
        static::assertIsArray($result[2]);
        static::assertIsArray($result[3]);
        static::assertEquals($bigPolygon, $result[0][0]);
        static::assertEquals(1, $result[0][1]);
        static::assertEquals($smallPolygon, $result[1][0]);
        static::assertEquals(1, $result[1][1]);
        static::assertEquals($polygonW, $result[2][0]);
        static::assertEquals(0, $result[2][1]);
        static::assertEquals($holeyPolygon, $result[3][0]);
        static::assertEquals(1, $result[3][1]);
    }
}
