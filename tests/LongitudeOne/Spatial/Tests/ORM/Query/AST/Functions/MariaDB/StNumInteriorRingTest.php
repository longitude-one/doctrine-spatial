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
use LongitudeOne\Spatial\Tests\Helper\PersistantPolygonHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_NumInteriorRing DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre-tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 *
 * @coversDefaultClass
 */
#[Group('mariadb-only')]
class StNumInteriorRingTest extends PersistOrmTestCase
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
    public function testSelectStNumInteriorRing(): void
    {
        $bigPolygon = $this->persistBigPolygon();
        $smallPolygon = $this->persistSmallPolygon();
        $holeyPolygon = $this->persistHoleyPolygon();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p, MariaDB_NumInteriorRings(p.polygon) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p'
        );
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertEquals($bigPolygon, $result[0][0]);
        static::assertEquals(0, $result[0][1]);
        static::assertEquals($smallPolygon, $result[1][0]);
        static::assertEquals(0, $result[1][1]);
        static::assertEquals($holeyPolygon, $result[2][0]);
        static::assertEquals(1, $result[2][1]);
    }

    /**
     * Test a DQL containing function to test in the predicate.
     */
    #[Group('geometry')]
    public function testStNumInteriorRingInPredicate(): void
    {
        $this->persistBigPolygon();
        $this->persistSmallPolygon();
        $holeyPolygon = $this->persistHoleyPolygon();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p WHERE MariaDB_NumInteriorRings(p.polygon) = :p'
        );
        $query->setParameter('p', 1);
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertEquals($holeyPolygon, $result[0]);
    }
}
