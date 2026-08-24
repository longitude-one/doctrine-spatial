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
use LongitudeOne\Spatial\ORM\Query\AST\Functions\PostgreSql\SpDWithin;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_DWithin DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(SpDWithin::class)]
#[Group('dql')]
#[Group('pgsql-only')]
class SpDWithinTest extends PersistOrmTestCase
{
    use PersistantPointHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->usesEntity(self::GEOGRAPHY_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testSelectGeography(): void
    {
        $newYork = $this->persistNewYorkGeography();
        $losAngeles = $this->persistLosAngelesGeography();
        $dallas = $this->persistDallasGeography();

        $query = $this->getEntityManager()->createQuery(
            'SELECT g, PgSql_DWithin(g.geography, PgSQL_GeogFromText(:p), :d, :spheroid) FROM LongitudeOne\Spatial\Tests\Fixtures\GeographyEntity g'
        );

        $query->setParameter('p', 'POINT(-89.4 43.066667)', 'string');
        $query->setParameter('d', 2000000.0); // 2.000.000m=2.000km
        $query->setParameter('spheroid', true, 'boolean');

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertIsArray($result[0]);
        static::assertIsArray($result[1]);
        static::assertIsArray($result[2]);
        static::assertEquals($newYork, $result[0][0]);
        static::assertTrue($result[0][1]);
        static::assertEquals($losAngeles, $result[1][0]);
        static::assertFalse($result[1][1]);
        static::assertEquals($dallas, $result[2][0]);
        static::assertTrue($result[2][1]);
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testSelectGeometry(): void
    {
        $newYork = $this->persistNewYorkGeometry();
        $losAngeles = $this->persistLosAngelesGeometry();
        $dallas = $this->persistDallasGeometry();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p, PgSql_DWithin(p.point, ST_GeomFromText(:p), :d) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        );

        $query->setParameter('p', 'POINT(-89.4 43.066667)', 'string');
        $query->setParameter('d', 20.0);

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertIsArray($result[0]);
        static::assertIsArray($result[1]);
        static::assertIsArray($result[2]);
        static::assertEquals($newYork, $result[0][0]);
        static::assertTrue($result[0][1]);
        static::assertEquals($losAngeles, $result[1][0]);
        static::assertFalse($result[1][1]);
        static::assertEquals($dallas, $result[2][0]);
        static::assertTrue($result[2][1]);
    }
}
