<?php
/**
 * This file is part of the Doctrine Spatial extension.
 *
 * PHP 8.4 | 8.5
 * Doctrine ORM ^3.6
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2026
 * Copyright Longitude One 2020-2026
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
     * @param float|int|string $x    X, longitude
     * @param float|int|string $y    Y, latitude
     * @param null|int         $srid Spatial Reference System Identifier
     *
     * @throws InvalidValueException if x or y are invalid
     */
    protected function construct(float|int|string $x, float|int|string $y, ?int $srid = null): void
    {
        $this->setX($x)
            ->setY($y)
            ->setSrid($srid)
        ;
    }
}
