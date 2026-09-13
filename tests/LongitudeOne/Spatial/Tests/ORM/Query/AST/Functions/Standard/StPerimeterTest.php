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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\Standard;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\Standard\StPerimeter;
use LongitudeOne\Spatial\Tests\Helper\PersistantPolygonHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_Perimeter DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(StPerimeter::class)]
#[Group('dql')]
class StPerimeterTest extends PersistOrmTestCase
{
    use PersistantPolygonHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        $this->supportsPlatform(SQLServerPlatform::class);
        // TODO Check if MySQL doesn't support this function or if I missed it

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
        $this->persistHoleyPolygon();
        $this->persistPolygonW();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p WHERE ST_Perimeter(p.polygon) = :p'
        );
        $query->setParameter('p', 40);
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
        $bigPolyon = $this->persistBigPolygon();
        $smallPolygon = $this->persistSmallPolygon();
        $holeyPolygon = $this->persistHoleyPolygon();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p, ST_Perimeter(p.polygon) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p'
        );
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertIsArray($result[0]);
        static::assertIsArray($result[1]);
        static::assertIsArray($result[2]);
        static::assertEquals($bigPolyon, $result[0][0]);
        static::assertEquals(40, $result[0][1]);
        static::assertEquals($smallPolygon, $result[1][0]);
        static::assertEquals(8, $result[1][1]);
        static::assertEquals($holeyPolygon, $result[2][0]);
        static::assertEquals(48, $result[2][1]);
    }
}
