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

namespace LongitudeOne\Spatial\PHP\Types;

/**
 * Abstract LineString object for LINESTRING spatial types.
 */
abstract class AbstractLineString extends AbstractMultiPoint
{
    /**
     * Type of this geometry: Linestring.
     */
    public function getType(): string
    {
        return self::LINESTRING;
    }

    /**
     * This line string is closed when the first point is the same as the last point.
     */
    public function isClosed(): bool
    {
        if (count($this->points) < 2) {
            return false;
        }

        return $this->points[0] === $this->points[count($this->points) - 1];
    }
}
