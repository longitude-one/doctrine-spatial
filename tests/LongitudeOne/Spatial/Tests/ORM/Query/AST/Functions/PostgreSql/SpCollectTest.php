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
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\PostgreSql\SpCollect;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\Tests\Fixtures\PointEntity;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_Collect DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(SpCollect::class)]
#[Group('dql')]
#[Group('pgsql-only')]
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
     */
    #[Group('geometry')]
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
