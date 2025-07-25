<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
 * Doctrine ORM 2.19 | 3.1
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2025
 * Copyright Longitude One 2020-2025
 * Copyright 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\Spatial\ORM\Query\AST\Functions\Common;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\AbstractSpatialDQLFunction;

/**
 * ST_SRID DQL function.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 */
class ScSrid extends AbstractSpatialDQLFunction
{
    /**
     * Function SQL name getter.
     */
    protected function getFunctionName(): string
    {
        return 'ST_SRID';
    }

    /**
     * Maximum number of parameters for the spatial function.
     *
     * Be careful, this function is different from the standard function.
     * PostgreSQL and MariaDB do NOT respect the standard. The ST_SRID function has only one parameter.
     * So we created this common function. If you're looking for the standard function, please use Standard/SpSrid.
     *
     * @return int the inherited methods shall NOT return null, but 0 when function has no parameter
     */
    protected function getMaxParameter(): int
    {
        return 1;
    }

    /**
     * Minimum number of parameters for the spatial function.
     *
     * @return int the inherited methods shall NOT return null, but 0 when function has no parameter
     */
    protected function getMinParameter(): int
    {
        return 1;
    }

    /**
     * Get the platforms accepted.
     *
     * @since 2.0 This function replace the protected property platforms.
     * @since 5.0 This function returns the class-string[] instead of string[]
     *
     * @return class-string<AbstractPlatform>[] a non-empty array of accepted platforms
     */
    protected function getPlatforms(): array
    {
        return [PostgreSQLPlatform::class, MariaDBPlatform::class];
    }
}
