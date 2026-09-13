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
use LongitudeOne\Spatial\ORM\Query\AST\Functions\Standard\StPointOnSurface;
use LongitudeOne\Spatial\Tests\Helper\PersistantLineStringHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_PointOnSurface DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(StPointOnSurface::class)]
#[Group('dql')]
class StPointOnSurfaceTest extends PersistOrmTestCase
{
    use PersistantLineStringHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        $this->supportsPlatform(SQLServerPlatform::class);
        // TODO Check whether MySQL supports this function or whether it is missing from the registration.

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testFunction(): void
    {
        $straightLineString = $this->persistStraightLineString();
        $angularLineString = $this->persistAngularLineString();
        $ringLineString = $this->persistRingLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l, ST_AsText(ST_PointOnSurface(l.lineString)) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
        );
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertIsArray($result[0]);
        static::assertIsArray($result[1]);
        static::assertIsArray($result[2]);

        static::assertEquals($straightLineString, $result[0][0]);
        static::assertIsString($result[0][1]);
        static::assertStringStartsWith('POINT', $result[0][1]);
        static::assertEquals($angularLineString, $result[1][0]);
        static::assertIsString($result[1][1]);
        static::assertStringStartsWith('POINT', $result[1][1]);
        static::assertEquals($ringLineString, $result[2][0]);
        static::assertIsString($result[2][1]);
        static::assertStringStartsWith('POINT', $result[2][1]);
    }

    /**
     * Test a DQL containing function to test in the predicate.
     */
    #[Group('geometry')]
    public function testFunctionInPredicate(): void
    {
        $straightLineString = $this->persistStraightLineString();
        $this->persistAngularLineString();
        $this->persistRingLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT l FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l where  ST_Equals(ST_PointOnSurface(l.lineString), ST_GeomFromText(:p, 0)) = true'
        );
        $query->setParameter('p', 'POINT(2 2)', 'string');
        $result = $query->getResult();

        static::assertIsArray($result);
        if ($this->getPlatform() instanceof SQLServerPlatform) {
            static::markTestSkipped('This test is stopped, the request is valid, but the result are different from the other database server');
        }

        static::assertCount(1, $result);
        static::assertEquals($straightLineString, $result[0]);
    }
}
