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
abstract class AbstractPolygon extends AbstractGeometry
{
    /**
     * Polygons are rings.
     *
     * @var (float|int)[][][]
     */
    protected array $rings = [];

    /**
     * Abstract polygon constructor.
     *
     * @param ((float|int)[][]|LineStringInterface|MultiPointInterface|PointInterface[])[] $rings the polygons
     * @param null|int                                                                     $srid  Spatial Reference System Identifier
     *
     * @throws InvalidValueException When a ring is invalid
     */
    public function __construct(array $rings, ?int $srid = null)
    {
        $this->setRings($rings)
            ->setSrid($srid)
        ;
    }

    /**
     * Add a polygon to geometry.
     *
     * @param (float|int)[][]|LineStringInterface|MultiPointInterface|PointInterface[]|PolygonInterface $ring Ring to add to geometry
     *
     * @throws InvalidValueException when a ring is invalid
     */
    public function addRing(array|LineStringInterface|MultiPointInterface|PolygonInterface $ring): self
    {
        if ($ring instanceof PolygonInterface) {
            throw new InvalidValueException('You cannot add a Polygon to another one. Use a Multipolygon.');
        }
        $this->rings[] = $this->validateRingValue($ring);

        return $this;
    }

    /**
     * Polygon getter.
     *
     * @param int $index index of polygon, use -1 to get the last one
     */
    public function getRing(int $index): LineStringInterface
    {
        if (-1 == $index) {
            $index = count($this->rings) - 1;
        }

        /** @var class-string<LineStringInterface> $lineStringClass */
        $lineStringClass = $this->getNamespace().'\LineString';

        return new $lineStringClass($this->rings[$index], $this->srid);
    }

    /**
     * Rings getter.
     *
     * @return LineStringInterface[]
     */
    public function getRings(): array
    {
        $rings = [];

        for ($i = 0; $i < count($this->rings); ++$i) {
            $rings[] = $this->getRing($i);
        }

        return $rings;
    }

    /**
     * Type getter.
     *
     * @return string Polygon
     */
    public function getType(): string
    {
        return self::POLYGON;
    }

    /**
     * Rings fluent setter.
     *
     * @param ((float|int)[][]|LineStringInterface|MultiPointInterface|PointInterface[])[] $rings Rings to set
     *
     * @throws InvalidValueException when a ring is invalid
     */
    public function setRings(array $rings): self
    {
        $this->rings = $this->validatePolygonValue($rings);

        return $this;
    }

    /**
     * Converts rings to array.
     *
     * @return (AbstractLineString|(AbstractPoint|(float|int)[])[])[] array of line-strings or array² of points...
     */
    public function toArray(): array
    {
        return $this->rings;
    }
}
