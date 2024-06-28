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

namespace LongitudeOne\Spatial\PHP\Types\Geography;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\AbstractPoint;
use LongitudeOne\Spatial\PHP\Types\GeodeticInterface;
use LongitudeOne\Spatial\PHP\Types\PointInterface;

/**
 * Point object for the POINT geography type.
 */
class Point extends AbstractPoint implements GeodeticInterface, GeographyInterface, PointInterface
{
    /**
     * X setter.
     *
     * @param float|int|string $x X coordinate
     *
     * @throws InvalidValueException when y is not in range of accepted value, or is totally invalid
     */
    public function setX(float|int|string $x): static
    {
        // TODO #67 - Trigger a deprecation notice when using this method. Advice to use setLongitude instead.
        return parent::setLongitude($x);
    }

    /**
     * Y setter.
     *
     * @param float|int|string $y the Y coordinate
     *
     * @throws InvalidValueException when y is not in range of accepted value, or is totally invalid
     */
    public function setY(float|int|string $y): static
    {
        // TODO #67 - Trigger a deprecation notice when using this method. Advice to use setLongitude instead.
        return parent::setLatitude($y);
    }

    /**
     * Point internal constructor.
     *
     * It uses Longitude and Latitude setters.
     *
     * @param string   $x    X, longitude
     * @param string   $y    Y, latitude
     * @param null|int $srid Spatial Reference System Identifier
     *
     * @throws InvalidValueException if x or y are invalid
     */
    protected function construct(string $x, string $y, ?int $srid = null): void
    {
        $this->setLongitude($x)
            ->setLatitude($y)
            ->setSrid($srid)
        ;
    }
}
