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

namespace LongitudeOne\Spatial\PHP\Types;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\RangeException;
use LongitudeOne\Spatial\PHP\Types\Utils\CartesianTrait;
use LongitudeOne\Spatial\PHP\Types\Utils\GeodesicTrait;
use LongitudeOne\Spatial\PHP\Types\Utils\GeoParseTrait;

/**
 * Abstract point object for POINT spatial types.
 *
 * @see https://stackoverflow.com/questions/7309121/preferred-order-of-writing-latitude-longitude-tuples
 * @see https://docs.geotools.org/latest/userguide/library/referencing/order.html
 */
abstract class AbstractPoint extends AbstractGeometry implements PointInterface
{
    use CartesianTrait;
    use GeodesicTrait;
    use GeoParseTrait;

    /**
     * The X coordinate or the longitude.
     */
    protected float|int $x;

    /**
     * The Y coordinate or the latitude.
     */
    protected float|int $y;

    /**
     * Latitude getter.
     */
    public function getLatitude(): float|int
    {
        return $this->getY();
    }

    /**
     * Longitude getter.
     */
    public function getLongitude(): float|int
    {
        return $this->getX();
    }

    /**
     * Type getter.
     */
    public function getType(): string
    {
        return self::POINT;
    }

    /**
     * X getter. (Longitude getter).
     */
    public function getX(): float|int
    {
        return $this->x;
    }

    /**
     * Y getter. Latitude getter.
     */
    public function getY(): float|int
    {
        return $this->y;
    }

    /**
     * Latitude fluent setter.
     *
     * @param float|int|string $latitude the new latitude of point
     *
     * @throws InvalidValueException when latitude is not valid
     */
    public function setLatitude(float|int|string $latitude): static
    {
        try {
            $geodesicCoordinate = $this->setGeodesicCoordinate($latitude, -90, 90);
        } catch (RangeException $e) {
            throw new InvalidValueException(sprintf(InvalidValueException::OUT_OF_RANGE_LATITUDE, $latitude), $e->getCode(), $e);
        }

        $this->y = $geodesicCoordinate;

        return $this;
    }

    /**
     * Longitude setter.
     *
     * @param float|int|string $longitude the new longitude
     *
     * @throws InvalidValueException when longitude is not valid
     */
    public function setLongitude(float|int|string $longitude): static
    {
        try {
            $geodesicCoordinate = $this->setGeodesicCoordinate($longitude, -180, 180);
        } catch (RangeException $e) {
            throw new InvalidValueException(sprintf(InvalidValueException::OUT_OF_RANGE_LONGITUDE, $longitude), $e->getCode(), $e);
        }

        $this->x = $geodesicCoordinate;

        return $this;
    }

    /**
     * X setter. (Latitude setter).
     *
     * @param float|int|string $x the new X
     *
     * @throws InvalidValueException when x is not valid
     */
    public function setX(float|int|string $x): static
    {
        $this->x = $this->setCartesianCoordinate($x);

        return $this;
    }

    /**
     * Y setter. Longitude Setter.
     *
     * @param float|int|string $y the new Y value
     *
     * @throws InvalidValueException when Y is invalid, not in valid range
     */
    public function setY(float|int|string $y): static
    {
        $this->y = $this->setCartesianCoordinate($y);

        return $this;
    }

    /**
     * Convert point into an array X, Y.
     * Latitude, longitude.
     *
     * @return array{0 : float|int, 1 : float|int}
     */
    public function toArray(): array
    {
        return [$this->x, $this->y];
    }
}
