<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2024
 * Copyright Longitude One 2020-2024
 * Copyright 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace LongitudeOne\Spatial\PHP\Types;

use LongitudeOne\Spatial\Exception\InvalidValueException;

/**
 * Abstract MultiPoint object for MULTIPOINT spatial types.
 */
abstract class AbstractMultiPoint extends AbstractGeometry
{
    /**
     * @var array[]
     */
    protected $points = [];

    /**
     * Abstract multipoint constructor.
     *
     * @param PointInterface[]|array[]|int[] $points array of point
     * @param int|null                       $srid   Spatial Reference System Identifier
     *
     * @throws InvalidValueException when a point is not valid
     */
    public function __construct(array $points, $srid = null)
    {
        $this->setPoints($points)
            ->setSrid($srid)
        ;
    }

    /**
     * Add a point to geometry.
     *
     * @param AbstractPoint|array $point Point to add to geometry
     *
     * @return self
     *
     * @throws InvalidValueException when the point is not valid
     */
    public function addPoint($point)
    {
        $this->points[] = $this->validatePointValue($point);

        return $this;
    }

    /**
     * Point getter.
     *
     * @param int $index index of the point to retrieve. -1 to get last point.
     *
     * @return AbstractPoint
     */
    public function getPoint($index)
    {
        switch ($index) {
            case -1:
                $point = $this->points[count($this->points) - 1];
                break;
            default:
                $point = $this->points[$index];
                break;
        }

        $pointClass = $this->getNamespace().'\Point';

        return new $pointClass($point[0], $point[1], $this->srid);
    }

    /**
     * Points getter.
     *
     * @return AbstractPoint[]
     */
    public function getPoints()
    {
        $points = [];

        for ($i = 0; $i < count($this->points); ++$i) {
            $points[] = $this->getPoint($i);
        }

        return $points;
    }

    /**
     * Type getter.
     *
     * @return string Multipoint
     */
    public function getType()
    {
        return self::MULTIPOINT;
    }

    /**
     * Points fluent setter.
     *
     * @param AbstractPoint[]|array[] $points the points
     *
     * @return self
     *
     * @throws InvalidValueException when a point is invalid
     */
    public function setPoints($points)
    {
        $this->points = $this->validateMultiPointValue($points);

        return $this;
    }

    /**
     * Convert multipoint to array.
     *
     * @return array[]
     */
    public function toArray()
    {
        return $this->points;
    }
}
