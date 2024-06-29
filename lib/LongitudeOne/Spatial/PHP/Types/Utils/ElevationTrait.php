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

/**
 * Elevation trait.
 *
 * This trait is used by Point3D and Point4D classes to manage the elevation.
 *
 * @internal
 */
trait ElevationTrait
{
    /**
     * The z coordinate cannot be null.
     */
    private float|int $z;

    /**
     * Get the z coordinate.
     */
    public function getZ(): float|int
    {
        return $this->z;
    }

    /**
     * Set the z coordinate.
     *
     * @param float|int $z The z coordinate
     */
    public function setZ(float|int $z): self
    {
        $this->z = $z;

        return $this;
    }
}
