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

class Point4D extends Point3D implements Point3DInterface
{
    private \DateTimeInterface $dateTime;

    private const DATE_FORMAT = 'c';

    public function __construct(float|int|string $x, float|int|string $y, float|int $z, \DateTimeInterface $dateTime, ?int $srid = null)
    {
        parent::__construct($x, $y, $z, $srid);

        $this->setDateTime($dateTime);
    }

    public function getDateTime(): \DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getType(): string
    {
        return SpatialInterface::POINT_ZM;
    }


    public function toArray(): array
    {
        return [$this->x, $this->y, $this->getZ(), $this->dateTime->format(self::DATE_FORMAT)];
    }

    public function __toString(): string
    {
        if (null === $this->getSrid()) {
            return sprintf('%s(%F %F %F)', $this->getType(), $this->x, $this->y, $this->getZ());
        }

        return sprintf('%s(SRID=%d;%F %F %F)', $this->getType(), $this->getSrid(), $this->x, $this->y, $this->getZ());
    }

    public function setDateTime(\DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }
}
