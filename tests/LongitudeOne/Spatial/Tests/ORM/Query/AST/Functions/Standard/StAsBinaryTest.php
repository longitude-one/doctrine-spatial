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
use LongitudeOne\Spatial\ORM\Query\AST\Functions\Standard\StAsBinary;
use LongitudeOne\Spatial\Tests\Helper\PersistantLineStringHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_AsBinary DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(StAsBinary::class)]
#[Group('dql')]
class StAsBinaryTest extends PersistOrmTestCase
{
    use PersistantLineStringHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
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
    public function testStAsBinary(): void
    {
        $this->persistStraightLineString();
        $this->persistAngularLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_AsBinary(l.lineString) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l'
        );
        $result = $query->getResult();

        $expectedA = '010200000003000000000000000000000000000000000000000000000000000040000000000000004000000000000014400000000000001440';
        $expectedB = '0102000000030000000000000000000840000000000000084000000000000010400000000000002e4000000000000014400000000000003640';

        static::assertIsArray($result);
        static::assertIsArray($result[0]);
        static::assertIsArray($result[1]);

        if ($this->getPlatform() instanceof MySQLPlatform || $this->getPlatform() instanceof MariaDBPlatform || $this->getPlatform() instanceof SQLServerPlatform) {
            static::assertEquals(pack('H*', $expectedA), $result[0][1]);
            static::assertEquals(pack('H*', $expectedB), $result[1][1]);
        }

        if ($this->getPlatform() instanceof PostgreSQLPlatform) {
            static::assertIsResource($result[0][1]);
            $actual = stream_get_contents($result[0][1]);
            static::assertNotFalse($actual, 'An error happen with the first parameter of stream_get_contents function');
            static::assertEquals($expectedA, bin2hex($actual));

            static::assertIsResource($result[1][1]);
            $actual = stream_get_contents($result[1][1]);
            static::assertNotFalse($actual, 'An error happen with the first parameter of stream_get_contents function');
            static::assertEquals($expectedB, bin2hex($actual));
        }
    }
}
