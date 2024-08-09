<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
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

namespace LongitudeOne\Spatial\ORM\Query\AST\Functions\MariaDB;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\AbstractSpatialDQLFunction;

/**
 * ST_NumInteriorRings DQL function.
 *
 * MariaDB does not implements ST_NumInteriorRing, but ST_NumInteriorRings
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 */
class StNumInteriorRings extends AbstractSpatialDQLFunction
{
    /**
     * Function SQL name getter.
     *
     * @since 2.0 This function replace the protected property functionName.
     */
    protected function getFunctionName(): string
    {
        return 'ST_NumInteriorRings';
    }

    /**
     * Maximum number of parameters for the spatial function.
     *
     * @return int the inherited methods shall NOT return null, but 0 when function has no parameter
     * @since 2.0 This function replace the protected property maxGeomExpr.
     *
     */
    protected function getMaxParameter(): int
    {
        return 1;
    }

    /**
     * Minimum number of parameters for the spatial function.
     *
     * @return int the inherited methods shall NOT return null, but 0 when function has no parameter
     * @since 2.0 This function replace the protected property minGeomExpr.
     *
     */
    protected function getMinParameter(): int
    {
        return 1;
    }

    /**
     * Get the platforms accepted.
     *
     * @return class-string<AbstractPlatform>[] a non-empty array of accepted platforms
     * @since 5.0 This function returns the class-string[] instead of string[]
     *
     * @since 2.0 This function replace the protected property platforms.
     */
    protected function getPlatforms(): array
    {
        return [MariaDBPlatform::class];
    }
}
