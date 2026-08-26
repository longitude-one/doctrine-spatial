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

namespace LongitudeOne\Spatial\DBAL\Types;

use LongitudeOne\Spatial\DBAL\Platform\MariaDB;
use LongitudeOne\Spatial\DBAL\Platform\MySql;
use LongitudeOne\Spatial\DBAL\Platform\PlatformInterface;
use LongitudeOne\Spatial\DBAL\Platform\PostgreSql;
use LongitudeOne\Spatial\DBAL\Platform\SqlServer;

/**
 * Doctrine GEOMETRY type.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 */
class GeometryType extends AbstractSpatialType
{
    /**
     * Return an array of all platform supporting the current type.
     *
     * @return class-string<PlatformInterface>[]
     */
    protected function getSupportedPlatforms(): array
    {
        return [
            MariaDB::class,
            MySql::class,
            PostgreSql::class,
            SqlServer::class,
        ];
    }
}
