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

namespace LongitudeOne\Spatial\PHP\Types\Geometry;

use LongitudeOne\Spatial\PHP\Types\CartesianTrait;
use LongitudeOne\Spatial\PHP\Types\Point3DInterface;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;

class Point3D extends Point implements Point3DInterface
{
    use CartesianTrait;

    private float|int $z;

    public function __construct(float|int|string $x, float|int|string $y, float|int $z, ?int $srid = null)
    {
        parent::__construct($x, $y, $srid);
        $this->setZ($z);
    }

    public function getType(): string
    {
        return SpatialInterface::POINT_Z;
    }

    public function getZ(): null|float|int
    {
        return $this->z;
    }

    public function setZ(float|int $z): Point3DInterface
    {
        $this->z = $z;

        return $this;
    }

    public function toArray(): array
    {
        return [$this->x, $this->y, $this->z];
    }

    public function __toString(): string
    {
        if (null === $this->getSrid()) {
            return sprintf('%s(%F %F %F)', $this->getType(), $this->x, $this->y, $this->z);
        }

        return sprintf('%s(SRID=%d;%F %F %F)', $this->getType(), $this->getSrid(), $this->x, $this->y, $this->z);
    }
}
