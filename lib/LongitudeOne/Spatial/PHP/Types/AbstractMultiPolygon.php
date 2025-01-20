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

namespace LongitudeOne\Spatial\PHP\Types;

use LongitudeOne\Spatial\Exception\InvalidValueException;

/**
 * Abstract Polygon object for POLYGON spatial types.
 */
abstract class AbstractMultiPolygon extends AbstractGeometry
{
    /**
     * @var ((float|int)[][][]|LineStringInterface[]|MultiPointInterface[]|PointInterface[][]|PolygonInterface)[]
     */
    protected array $polygons = [];

    /**
     * AbstractMultiPolygon constructor.
     *
     * @param ((float|int)[][][]|LineStringInterface[]|MultiPointInterface[]|PointInterface[][]|PolygonInterface)[] $polygons Polygons
     * @param null|int                                                                                              $srid     Spatial Reference System Identifier
     *
     * @throws InvalidValueException when a polygon is invalid
     */
    public function __construct(array $polygons, ?int $srid = null)
    {
        $this
            ->setPolygons($polygons)
            ->setSrid($srid)
        ;
    }

    /**
     * Add a polygon to geometry.
     *
     * @param ((float|int)[][]|LineStringInterface|MultiPointInterface|PointInterface[])[]|PolygonInterface $polygon polygon to add
     *
     * @throws InvalidValueException when polygon is not an array nor an AbstractPolygon
     */
    public function addPolygon(array|PolygonInterface $polygon): self
    {
        if ($polygon instanceof AbstractPolygon) {
            $polygon = $polygon->toArray();
        }

        if (!is_array($polygon)) {
            throw new InvalidValueException('AbstractMultiPolygon::addPolygon only accepts AbstractPolygon or an array as parameter');
        }

        $this->polygons[] = $this->validatePolygonValue($polygon);

        return $this;
    }

    /**
     * Polygon getter.
     *
     * @param int $index Index of polygon, use -1 to get the last one
     */
    public function getPolygon(int $index): PolygonInterface
    {
        // TODO throw an error when index is out of range
        // TODO throw an error when $this->polygons is empty
        // TODO replace by a function to be compliant with -1, -2, etc.
        if (-1 == $index) {
            $index = count($this->polygons) - 1;
        }

        /** @var class-string<PolygonInterface> $polygonClass */
        $polygonClass = $this->getNamespace().'\Polygon';

        return new $polygonClass($this->polygons[$index], $this->srid);
    }

    /**
     * Polygons getter.
     *
     * @return PolygonInterface[]
     */
    public function getPolygons(): array
    {
        $polygons = [];

        for ($i = 0; $i < count($this->polygons); ++$i) {
            $polygons[] = $this->getPolygon($i);
        }

        return $polygons;
    }

    /**
     * Type getter.
     *
     * @return string MultiPolygon
     */
    public function getType(): string
    {
        return self::MULTIPOLYGON;
    }

    /**
     * Polygon setter.
     *
     * @param ((float|int)[][][]|LineStringInterface[]|MultiPointInterface[]|PointInterface[][]|PolygonInterface)[] $polygons polygons to set
     *
     * @throws InvalidValueException when a polygon is invalid
     */
    public function setPolygons(array $polygons): self
    {
        $this->polygons = $this->validateMultiPolygonValue($polygons);

        return $this;
    }

    /**
     * Convert Polygon into an array.
     *
     * @return ((float|int)[][][]|LineStringInterface[]|MultiPointInterface[]|PointInterface[][]|PolygonInterface)[]
     */
    public function toArray(): array
    {
        return $this->polygons;
    }
}
