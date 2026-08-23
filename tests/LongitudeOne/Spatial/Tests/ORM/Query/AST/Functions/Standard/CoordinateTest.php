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

use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_X and ST_Y DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 *
 * @coversDefaultClass
 */
#[Group('dql')]
class CoordinateTest extends PersistOrmTestCase
{
    use PersistantPointHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        $this->supportsPlatform(MariaDBPlatform::class);
        $this->supportsPlatform(MySQLPlatform::class);
        $this->supportsPlatform(SQLServerPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testSelectCoordinates(): void
    {
        $pointO = $this->persistPointO();
        $pointA = $this->persistPointA();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p, ST_X(p.point), ST_Y(p.point) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        );
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(2, $result);
        static::assertEquals($pointO, $result[0][0]);
        static::assertEquals(0, $result[0][1]);
        static::assertEquals(0, $result[0][2]);
        static::assertEquals($pointA, $result[1][0]);
        static::assertEquals(1, $result[1][1]);
        static::assertEquals(2, $result[1][2]);
    }
}
