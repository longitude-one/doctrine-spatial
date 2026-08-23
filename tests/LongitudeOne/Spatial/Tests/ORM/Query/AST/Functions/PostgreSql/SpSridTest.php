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

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\Tests\Helper\PersistantLineStringHelperTrait;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_SRID PostGreSQL function tests.
 *
 * Be warned that PostGreSQL is not respecting the ISO/IEC 13249-3 standard.
 * PostGreSQL only accepts one parameter with ST_SRID function.
 *
 * ```sql
 * SELECT ST_SRID(g.point, 4326) FROM PointEntity g
 * [42883] ERROR: function st_srid(geometry, integer) does not exist.
 * Indice: No function matches the given name and argument types.
 * You might need to add explicit type casts.
 * ```
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 *
 * @coversDefaultClass
 */
#[Group('postgis-only')]
class SpSridTest extends PersistOrmTestCase
{
    use PersistantLineStringHelperTrait;
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
    public function testFunctionWithGeography(): void
    {
        $this->persistGeographyLosAngeles();

        $query = $this->getEntityManager()->createQuery(
            'SELECT PgSQL_SRID(g.geography) FROM LongitudeOne\Spatial\Tests\Fixtures\GeographyEntity g'
        );
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        if ($this->getPlatform() instanceof MySQLPlatform) {
            // MySQL is returning 0 insteadof 4326
            static::markTestSkipped('SRID not yet implemented in Abstraction of MySQL');
        }

        static::assertSame(4326, $result[0][1]);
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testFunctionWithGeometry(): void
    {
        $this->createAndPersistGeometricPoint('A', '1', '1', 2154);

        $query = $this->getEntityManager()->createQuery(
            'SELECT PgSQL_SRID(g.point) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity g'
        );
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertIsArray($result[0]);
        static::assertCount(1, $result[0]);
        static::assertSame(2154, $result[0][1]);
    }
}
