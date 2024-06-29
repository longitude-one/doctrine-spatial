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
use LongitudeOne\Spatial\PHP\Types\AbstractPoint;
use LongitudeOne\Spatial\PHP\Types\CartesianInterface;
use LongitudeOne\Spatial\PHP\Types\PointInterface;

/**
 * Point class for the POINT geometry type.
 */
class Point extends AbstractPoint implements CartesianInterface, GeometryInterface, PointInterface
{
    /**
     * Point constructor.
     *
     * It uses X and Y setters.
     *
     * @param float|int|string $x    X, longitude
     * @param float|int|string $y    Y, latitude
     * @param null|int         $srid Spatial Reference System Identifier
     *
     * @throws InvalidValueException if x or y are invalid
     */
    public function __construct(float|int|string $x, float|int|string $y, ?int $srid = null)
    {
        $this->setX($x)
            ->setY($y)
            ->setSrid($srid)
        ;
    }
}
