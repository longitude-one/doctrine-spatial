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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\MySql;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\MySql\SpMbrTouches;
use LongitudeOne\Spatial\Tests\Helper\PersistantPolygonHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * MySQL_MbrTouches DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(SpMbrTouches::class)]
#[Group('dql')]
#[Group('mysql-only')]
class SpMbrTouchesTest extends PersistOrmTestCase
{
    use PersistantPolygonHelperTrait;

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
     * Test a DQL function in the predicate.
     */
    #[Group('geometry')]
    public function testFunctionInPredicate(): void
    {
        $bigPolygon = $this->persistBigPolygon();
        $this->persistSmallPolygon();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p WHERE MySQL_MbrTouches(p.polygon, ST_GeomFromText(:p)) = true'
        );
        $query->setParameter('p', 'LINESTRING(0 0, 0 10)', 'string');
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertEquals($bigPolygon, $result[0]);
    }

    /**
     * Test a DQL containing function to test.
     */
    #[Group('geometry')]
    public function testFunctionInSelect(): void
    {
        $this->persistBigPolygon();
        $this->persistSmallPolygon();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT MySQL_MbrTouches(p.polygon, ST_GeomFromText(:p)) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p'
        );
        $query->setParameter('p', 'LINESTRING(0 0, 0 10)', 'string');
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertIsArray($result[0]);
        static::assertIsArray($result[1]);
        static::assertEquals(1, $result[0][1]);
        static::assertEquals(0, $result[1][1]);
    }
}
