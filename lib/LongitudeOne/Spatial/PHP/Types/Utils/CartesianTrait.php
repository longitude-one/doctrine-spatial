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

namespace LongitudeOne\Spatial\PHP\Types\Utils;

use LongitudeOne\Spatial\Exception\InvalidValueException;

/**
 * Cartesian trait.
 *
 * @internal
 */
trait CartesianTrait
{
    /**
     * Set a cartesian coordinate.
     * Abscissa or ordinate.
     *
     * @param float|int|string $coordinate the coordinate to set
     *
     * @throws InvalidValueException when coordinate is invalid, RangeException is never thrown
     */
    private function setCartesianCoordinate(float|int|string $coordinate): float|int
    {
        if (is_integer($coordinate) || is_float($coordinate)) {
            // We don't check the range of the value.
            return $coordinate;
        }

        // $coordinate is a string, let's use the geo-parser.
        return $this->geoParse($coordinate);
    }
}
