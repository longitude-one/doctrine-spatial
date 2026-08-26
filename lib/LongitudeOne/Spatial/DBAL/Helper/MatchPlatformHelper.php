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

namespace LongitudeOne\Spatial\DBAL\Helper;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use LongitudeOne\Spatial\DBAL\Platform\MariaDB;
use LongitudeOne\Spatial\DBAL\Platform\MySql;
use LongitudeOne\Spatial\DBAL\Platform\PlatformInterface;
use LongitudeOne\Spatial\DBAL\Platform\PostgreSql;
use LongitudeOne\Spatial\DBAL\Platform\SqlServer;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;

/**
 * Abstract spatial DQL function.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * This spatial helper class provide methods to get the corresponding spatial platform of a given doctrine platform.
 */
final class MatchPlatformHelper
{
    /**
     * Get the spatial corresponding platform of the given doctrine platform.
     *
     * @param AbstractPlatform $platform the doctrine platform
     *
     * @return PlatformInterface the corresponding platform
     *
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    public function getSpatialPlatform(AbstractPlatform $platform): PlatformInterface
    {
        if ($platform instanceof PostgreSQLPlatform) {
            return new PostgreSql();
        }
        if ($platform instanceof MySQLPlatform) {
            return new MySql();
        }
        if ($platform instanceof MariaDBPlatform) {
            return new MariaDB();
        }
        if ($platform instanceof SQLServerPlatform) {
            return new SqlServer();
        }

        throw new UnsupportedPlatformException(
            sprintf('The DBAL "%s" is not currently associated with one of our supported platforms.', $platform::class)
        );
    }
}
