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

namespace LongitudeOne\Spatial\Tests\DBAL\Helper;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\DB2Platform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use LongitudeOne\Spatial\DBAL\Helper\MatchPlatformHelper;
use LongitudeOne\Spatial\DBAL\Platform\MariaDB;
use LongitudeOne\Spatial\DBAL\Platform\MySql;
use LongitudeOne\Spatial\DBAL\Platform\PlatformInterface;
use LongitudeOne\Spatial\DBAL\Platform\PostgreSql;
use LongitudeOne\Spatial\DBAL\Platform\SqlServer;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Doctrine platform matcher tests.
 *
 * @internal
 */
#[CoversClass(MatchPlatformHelper::class)]
#[Group('php')]
class MatchPlatformHelperTest extends TestCase
{
    /**
     * Test matching a supported Doctrine platform.
     *
     * @param AbstractPlatform $doctrinePlatform Doctrine platform
     * @param string           $expectedPlatform Expected spatial platform class
     */
    #[DataProvider('platformProvider')]
    public function testMatchesSupportedPlatform(AbstractPlatform $doctrinePlatform, string $expectedPlatform): void
    {
        $spatialPlatform = (new MatchPlatformHelper())->getSpatialPlatform($doctrinePlatform);

        static::assertSame($expectedPlatform, $spatialPlatform::class);
    }

    /**
     * @return iterable<string, array{AbstractPlatform, class-string<PlatformInterface>}>
     */
    public static function platformProvider(): iterable
    {
        yield 'MariaDB' => [new MariaDBPlatform(), MariaDB::class];

        yield 'MySQL' => [new MySQLPlatform(), MySql::class];

        yield 'PostgreSQL' => [new PostgreSQLPlatform(), PostgreSql::class];

        yield 'SQL Server' => [new SQLServerPlatform(), SqlServer::class];
    }

    /**
     * Test rejecting an unsupported Doctrine platform.
     */
    public function testRejectsUnsupportedPlatform(): void
    {
        $this->expectException(UnsupportedPlatformException::class);
        $this->expectExceptionMessageMatches('/^The DBAL ".+" is not currently associated with one of our supported platforms\.$/');

        (new MatchPlatformHelper())->getSpatialPlatform(new DB2Platform());
    }
}
