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

namespace LongitudeOne\Spatial\PHP\Types\Geometry;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\AbstractPoint;
use LongitudeOne\Spatial\PHP\Types\CartesianInterface;
use LongitudeOne\Spatial\PHP\Types\PointInterface;

/**
 * Point object for the POINT geometry type.
 */
class Point extends AbstractPoint implements CartesianInterface, GeometryInterface, PointInterface
{
    /**
     * Point internal constructor.
     *
     * It uses X and Y setters.
     *
     * @param string   $x    X, longitude
     * @param string   $y    Y, latitude
     * @param null|int $srid Spatial Reference System Identifier
     *
     * @throws InvalidValueException if x or y are invalid
     */
    protected function construct(string $x, string $y, ?int $srid = null): void
    {
        $this->setX($x)
            ->setY($y)
            ->setSrid($srid)
        ;
    }
}
