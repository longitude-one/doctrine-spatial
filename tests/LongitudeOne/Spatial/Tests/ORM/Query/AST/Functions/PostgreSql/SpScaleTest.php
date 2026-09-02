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
use LongitudeOne\Spatial\ORM\Query\AST\Functions\PostgreSql\SpScale;
use LongitudeOne\Spatial\Tests\Helper\PersistantLineStringHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * SP_Scale DQL function tests.
 * This function is not issue from the OGC, but it is useful for Database postgresql.
 *
 * @see https://postgis.net/docs/ST_Scale.html
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(SpScale::class)]
#[Group('dql')]
#[Group('pgsql-only')]
class SpScaleTest extends PersistOrmTestCase
{
    use PersistantLineStringHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing ST_Scale with a geometry scale factor.
     */
    #[Group('geometry')]
    public function testFunctionInSelectWithGeometryScaleFactor(): void
    {
        $straightLineString = $this->persistStraightLineString();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l, ST_AsText(PgSQL_Scale(l.lineString, ST_GeomFromText(:factor))) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
        );
        $query->setParameter('factor', 'POINT(2 4)');
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertIsArray($result[0]);
        static::assertEquals($straightLineString, $result[0][0]);
        static::assertSame('LINESTRING(0 0,4 8,10 20)', $result[0][1]);
    }

    /**
     * Test a DQL containing ST_Scale with a geometry scale factor and origin.
     */
    #[Group('geometry')]
    public function testFunctionInSelectWithGeometryScaleFactorAndOrigin(): void
    {
        $straightLineString = $this->persistStraightLineString();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l, ST_AsText(PgSQL_Scale(l.lineString, ST_GeomFromText(:factor), ST_GeomFromText(:origin))) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
        );
        $query->setParameter('factor', 'POINT(2 4)');
        $query->setParameter('origin', 'POINT(1 2)');
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertIsArray($result[0]);
        static::assertEquals($straightLineString, $result[0][0]);
        static::assertSame('LINESTRING(-1 -6,3 2,9 14)', $result[0][1]);
    }

    /**
     * Test a DQL containing ST_Scale with X, Y and Z scale factors.
     */
    #[Group('geometry')]
    public function testFunctionInSelectWithThreeScaleFactors(): void
    {
        $straightLineString = $this->persistStraightLineString();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l, ST_AsText(PgSQL_Scale(l.lineString, :x, :y, :z)) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
        );
        $query->setParameter('x', 2);
        $query->setParameter('y', 4);
        $query->setParameter('z', 8);
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertIsArray($result[0]);
        static::assertEquals($straightLineString, $result[0][0]);
        static::assertSame('LINESTRING(0 0,4 8,10 20)', $result[0][1]);
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testFunctionInSelectWithTwoScaleFactors(): void
    {
        $straightLineString = $this->persistStraightLineString();
        $angularLineString = $this->persistAngularLineString();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l, ST_AsText(PgSQL_Scale(l.lineString, :x, :y)) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
        );
        $query->setParameter('x', 2);
        $query->setParameter('y', 4);
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(2, $result);
        static::assertIsArray($result[0]);
        static::assertIsArray($result[1]);
        static::assertEquals($straightLineString, $result[0][0]);
        static::assertSame('LINESTRING(0 0,4 8,10 20)', $result[0][1]);
        static::assertEquals($angularLineString, $result[1][0]);
        static::assertEquals('LINESTRING(6 12,8 60,10 88)', $result[1][1]);
    }
}
