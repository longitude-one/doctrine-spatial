<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP          8.1 | 8.2 | 8.3
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

namespace LongitudeOne\Spatial\PHP\Types;

interface Point3DInterface extends SpatialInterface
{
    /**
     * Point constructor.
     *
     * @param float|int|string $x    X coordinate
     * @param float|int|string $y    Y coordinate
     * @param float|int        $z    Z coordinate, elevation
     * @param null|int         $srid SRID
     */
    public function __construct(float|int|string $x, float|int|string $y, float|int $z, ?int $srid = null);

    /**
     * Get the Z coordinate, the elevation.
     */
    public function getZ(): null|float|int;

    /**
     * Set the Z coordinate, the elevation.
     *
     * @param float|int $z Z coordinate, elevation
     */
    public function setZ(float|int $z): self;

    /**
     * Convert point to its array representation.
     *
     * Array does NOT contain SpatialInterface, only floats and integers.
     *
     * @return (float|int)[]
     */
    public function toArray(): array;
}
