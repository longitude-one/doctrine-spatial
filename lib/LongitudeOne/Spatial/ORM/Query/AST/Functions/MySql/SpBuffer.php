<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
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

namespace LongitudeOne\Spatial\ORM\Query\AST\Functions\MySql;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\AbstractSpatialDQLFunction;

/**
 * Sp_Buffer DQL function.
 * MySQL ST_Buffer function is not able to receive a CHARACTER VARYING as third parameter.
 * This SQL function can only received the result of a ST_Buffer_Strategy function as third parameter.
 * So Sp_Buffer DQL function is specific to MySQL.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 */
class SpBuffer extends AbstractSpatialDQLFunction
{
    /**
     * Function SQL name getter.
     *
     * @since 2.0 This function replace the protected property functionName.
     */
    protected function getFunctionName(): string
    {
        return 'ST_Buffer';
    }

    /**
     * Maximum number of parameters for the spatial function.
     *
     * @since 2.0 This function replace the protected property maxGeomExpr.
     *
     * @return int the inherited methods shall NOT return null, but 0 when function has no parameter
     */
    protected function getMaxParameter(): int
    {
        return 3;
    }

    /**
     * Minimum number of parameters for the spatial function.
     *
     * @since 2.0 This function replace the protected property minGeomExpr.
     *
     * @return int the inherited methods shall NOT return null, but 0 when function has no parameter
     */
    protected function getMinParameter(): int
    {
        return 2;
    }

    /**
     * Get the platforms accepted.
     *
     * @since 2.0 This function replace the protected property platforms.
     * @since 5.0 This function returns the class-string[] instead of string[]
     *
     * @return class-string[] a non-empty array of accepted platforms
     */
    protected function getPlatforms(): array
    {
        return [MySQLPlatform::class];
    }
}
