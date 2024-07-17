<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\Standard;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\Tests\Helper\PersistantLineStringHelperTrait;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * ST_SRID DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 *
 * @coversDefaultClass
 */
class StSridTest extends PersistOrmTestCase
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
        $this->supportsPlatform(MySQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testFunctionWithGeography(): void
    {
        $this->persistGeographyLosAngeles();

        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_SRID(g.geography) FROM LongitudeOne\Spatial\Tests\Fixtures\GeographyEntity g'
        );
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        if ($this->getPlatform() instanceof MySQLPlatform) {
            // TODO MySQL is returning 0 insteadof 4326
            static::markTestSkipped('SRID not implemented in Abstraction of MySQL');
        }

        static::assertSame(4326, $result[0][1]);
    }

    /**
     * @group srid-2-parameters
     */
    public function testFunctionSqlGenerationWithTwoParameters(): void
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_SRID(g.geography, 2154) FROM LongitudeOne\Spatial\Tests\Fixtures\GeographyEntity g'
        );

        static::assertSame('SELECT ST_SRID(g0_.geography, 2154) AS sclr_0 FROM GeographyEntity g0_', $query->getSQL());
    }

    /**
     * @group srid-2-parameters
     */
    public function testFunctionWithGeometryAndChangedSrid(): void
    {
        $this->createAndPersistGeometricPoint('A', '1', '1', 2154);

        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_SRID(ST_SRID(g.point, 4326)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity g'
        );
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertIsArray($result[0]);
        static::assertCount(1, $result[0]);
        static::assertSame(4326, $result[0][1]);
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testFunctionWithGeometry(): void
    {
        $this->createAndPersistGeometricPoint('A', '1', '1', 2154);

        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_SRID(g.point) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity g'
        );
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertIsArray($result[0]);
        static::assertCount(1, $result[0]);
        if ($this->getPlatform() instanceof MySQLPlatform) {
            // MySQL is returning 0 insteadof 2154
            static::markTestSkipped('SRID not implemented in Abstraction of MySQL');
        }

        static::assertSame(2154, $result[0][1]);
    }
}
