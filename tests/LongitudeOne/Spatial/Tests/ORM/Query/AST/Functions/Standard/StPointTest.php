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
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_Point DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @internal
 *
 * @coversDefaultClass
 */
#[Group('dql')]
class StPointTest extends PersistOrmTestCase
{
    use PersistantPointHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        // SQL Server doesn't support this function
        // TODO Check if MySSQL doesn't support this function or if I missed this function

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the predicate.
     */
    #[Group('geometry')]
    public function testPredicate(): void
    {
        $this->persistToursLambert93(false);
        $pointO = $this->persistPointO();
        $this->persistPointA();
        $this->persistPointB();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p WHERE ST_EQUALS(p.point, ST_Point(:x, :y)) = true'
        );
        $query->setParameter('x', 0, 'integer');
        $query->setParameter('y', 0, 'integer');

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertEquals($pointO, $result[0]);
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testSelectWithSrid(): void
    {
        $tours = $this->persistToursLambert93();
        $this->persistGeometryParisLambert93();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p WHERE ST_EQUALS(p.point, ST_SetSRID(ST_Point(:x, :y), :srid)) = true'
        );

        $query->setParameter('x', 525375.21);
        $query->setParameter('y', 6701871.83);
        $query->setParameter('srid', 2154);

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertEquals($tours, $result[0]);
    }
}
