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

namespace LongitudeOne\Spatial\PHP\Types\Geometry;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Point3DInterface;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;
use LongitudeOne\Spatial\PHP\Types\Utils\ElevationTrait;

/**
 * Geometric 3D Point object for the POINT Z geometry type.
 */
class Point3D extends Point implements Point3DInterface
{
    use ElevationTrait;

    /**
     * Point3D constructor.
     *
     * @param float|int|string $x    The x coordinate
     * @param float|int|string $y    The y coordinate
     * @param float|int        $z    The z coordinate
     * @param null|int         $srid The SRID
     *
     * @throws InvalidValueException when coordinate is invalid, RangeException is never thrown
     */
    public function __construct(float|int|string $x, float|int|string $y, float|int $z, ?int $srid = null)
    {
        parent::__construct($x, $y, $srid);
        $this->setZ($z);
    }

    /**
     * Get the type of this geometry.
     */
    public function getType(): string
    {
        return SpatialInterface::POINT_Z;
    }

    /**
     * Convert the geometry coordinates to their array representation.
     *
     * @return array{0: float|int, 1: float|int, 2: float|int}
     */
    public function toArray(): array
    {
        return [$this->getX(), $this->getY(), $this->getZ()];
    }

    /**
     * Convert the geometry coordinates to their string representation.
     *
     * @example "42 43 44"
     */
    public function __toString(): string
    {
        return vsprintf('%s %s %s', $this->toArray());
    }
}
