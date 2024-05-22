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

use LongitudeOne\Spatial\Exception\InvalidValueException;

/**
 * Geodetic (or geographic) coordinates are spherical coordinates expressed in angular units (degrees).
 * Coordinates are longitude and latitude.
 */
interface GeodeticInterface
{
    public function getLatitude(): float|int;

    public function getLongitude(): float|int;

    /**
     * The interface doesn't fix the return type.
     * Usually, fluent setters return "self".
     * Geodetic interfaces are used in AbstractPoint. It can return a geometry interface or a geographic one.
     *
     * @throws InvalidValueException when latitude is out of range
     */
    public function setLatitude(float|int|string $latitude): GeodeticInterface|PointInterface;

    /**
     * The interface doesn't fix the return type.
     * Usually, fluent setters return "self".
     * Geodetic interfaces are used in AbstractPoint. It can return a geometry interface or a geographic one.
     *
     * @throws InvalidValueException when longitude is out of range
     */
    public function setLongitude(float|int|string $longitude): GeodeticInterface|PointInterface;
}
