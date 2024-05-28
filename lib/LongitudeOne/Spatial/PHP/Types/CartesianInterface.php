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

/**
 * Cartesian coordinates are planar coordinates expressed in linear units (meters).
 * It includes projected coordinates, map projection coordinates, grid coordinates.
 * Coordinates are x and y.
 *
 * Be aware that the setters of this interface don't throw exceptions when coordinates are out of range of the selected SRID.
 */
interface CartesianInterface
{
    /**
     * @return float|int the abscissa coordinate
     */
    public function getX(): float|int;

    /**
     * @return float|int the ordinate coordinate
     */
    public function getY(): float|int;

    /**
     * The interface doesn't fix the return type.
     * Usually, fluent setters return "self".
     * Cartesian interfaces are used in AbstractPoint. It can return a geometry interface or a geographic one.
     *
     * @param float|int|string $x the abscissa coordinate
     */
    public function setX(float|int|string $x): CartesianInterface|PointInterface;

    /**
     * The interface doesn't fix the return type.
     * Usually, fluent setters return "self".
     * Cartesian interfaces are used in AbstractPoint. It can return a geometry interface or a geographic one.
     *
     * @param float|int|string $y the ordinate coordinate
     */
    public function setY(float|int|string $y): CartesianInterface|PointInterface;
}
