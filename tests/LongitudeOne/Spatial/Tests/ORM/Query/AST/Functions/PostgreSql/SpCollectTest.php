<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP          8.1 | 8.2 | 8.3
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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\PostgreSql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\Tests\Fixtures\PointEntity;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * ST_Collect DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group dql
 * @group pgsql-only
 *
 * @internal
 *
 * @coversDefaultClass
 */
class SpCollectTest extends PersistOrmTestCase
{
    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @throws ORMException            when cache is not set
     * @throws OptimisticLockException when clear fails
     * @throws InvalidValueException   when geometries are not valid
     *
     * @group geometry
     */
    public function testFunctionSelect(): void
    {
        $entity = new PointEntity();
        $entity->setPoint(new Point(1, 2));
        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_AsText(PgSql_Collect(p.point, ST_GeomFromText(:p))) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        );
        $query->setParameter('p', 'POINT(-2 3)');
        $result = $query->getResult();

        $expected = [
            [1 => 'MULTIPOINT((1 2),(-2 3))'],
        ];

        static::assertEquals($expected, $result);
    }
}
