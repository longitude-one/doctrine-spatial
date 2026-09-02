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
use LongitudeOne\Spatial\ORM\Query\AST\Functions\Standard\StLineStringFromWkb;
use LongitudeOne\Spatial\Tests\Helper\PersistantGeometryHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_LineStringFromWKB DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(StLineStringFromWkb::class)]
#[Group('dql')]
class StLineStringFromWkbTest extends PersistOrmTestCase
{
    use PersistantGeometryHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::GEOMETRY_ENTITY);
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
        $this->persistGeometryStraightLine();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT g, St_AsText(ST_LineStringFromWkb(St_AsBinary(g.geometry), 0)) FROM LongitudeOne\Spatial\Tests\Fixtures\GeometryEntity g'
        );

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertIsArray($result[0]);
        static::assertIsString($result[0][1]);
        static::assertStringStartsWith('LINESTRING', $result[0][1]);
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testSelectWithSrid(): void
    {
        $this->persistGeometryStraightLine();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $dql = 'SELECT g, ST_SRID(ST_LineStringFromWkb(:wkb, :srid)) FROM LongitudeOne\Spatial\Tests\Fixtures\GeometryEntity g';
        if ($this->getPlatform() instanceof PostgreSQLPlatform) {
            $dql = 'SELECT g, PgSQL_SRID(ST_LineStringFromWkb(:wkb, :srid)) FROM LongitudeOne\Spatial\Tests\Fixtures\GeometryEntity g';
        }

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('wkb', hex2bin('010200000003000000000000000000000000000000000000000000000000000040000000000000004000000000000014400000000000001440'), 'blob');
        $query->setParameter('srid', 2154);

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertIsArray($result[0]);
        static::assertEquals(2154, $result[0][1]);
    }
}
