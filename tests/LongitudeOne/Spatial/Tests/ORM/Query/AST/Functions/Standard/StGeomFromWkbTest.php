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

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\Standard\StGeomFromWkb;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_GeomFromWkb DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(StGeomFromWkb::class)]
#[Group('dql')]
class StGeomFromWkbTest extends PersistOrmTestCase
{
    use PersistantPointHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        $this->supportsPlatform(MySQLPlatform::class);
        $this->supportsPlatform(SQLServerPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testSelect(): void
    {
        $this->skipIfMariaDbAndOrm29();

        $this->persistNewYorkGeometry(); // Unused fake point
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT t, ST_AsText(ST_GeomFromWkb(:wkb, 0)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity t'
        );
        $query->setParameter('wkb', hex2bin('0101000000000000000000F03F000000000000F0BF'), 'blob');

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertIsArray($result[0]);
        static::assertIsString($result[0][1]);
        static::assertStringStartsWith('POINT', $result[0][1]);
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testSelectWithSrid(): void
    {
        $this->skipIfMariaDbAndOrm29();

        $this->persistNewYorkGeometry(); // Unused fake point
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $dql = 'SELECT t, ST_SRID(ST_GeomFromWkb(:wkb, :srid)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity t';
        // PostGreSQL SRID function doesn't respect standard. We use its internal function.
        if ($this->getPlatform() instanceof PostgreSQLPlatform) {
            $dql = 'SELECT t, PgSql_SRID(ST_GeomFromWkb(:wkb, :srid)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity t';
        }

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('wkb', hex2bin('0101000000000000000000F03F000000000000F0BF'), 'blob');
        $query->setParameter('srid', 2154);

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertIsArray($result[0]);
        static::assertEquals(2154, $result[0][1]);
    }
}
