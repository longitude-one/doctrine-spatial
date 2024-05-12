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

namespace LongitudeOne\Spatial\PHP\Types;

use LongitudeOne\Spatial\Exception\InvalidValueException;

/**
 * Abstract MultiLineString object for MULTILINESTRING spatial types.
 */
abstract class AbstractMultiLineString extends AbstractGeometry
{
    /**
     * Array of line strings.
     *
     * @var (float|int)[][][]
     */
    protected array $lineStrings = [];

    /**
     * AbstractMultiLineString constructor.
     *
     * @param ((float|int)[][]|LineStringInterface|MultiPointInterface|PointInterface[])[] $lineStrings array of linestring
     * @param null|int                                                                     $srid        Spatial Reference System Identifier
     *
     * @throws InvalidValueException when rings contain an invalid linestring
     */
    public function __construct(array $lineStrings, $srid = null)
    {
        $this->setLineStrings($lineStrings)
            ->setSrid($srid)
        ;
    }

    /**
     * Add a linestring to geometry.
     *
     * @param (float|int)[][]|LineStringInterface|MultiPointInterface|PointInterface[] $lineString the line string to add to Geometry
     *
     * @throws InvalidValueException when linestring is not valid
     */
    public function addLineString(array|LineStringInterface|MultiPointInterface $lineString): self
    {
        $this->lineStrings[] = $this->validateLineStringValue($lineString);

        return $this;
    }

    /**
     * Return linestring at specified offset.
     *
     * @param int $index offset of line string to return. Use -1 to get last linestring.
     */
    public function getLineString(int $index): LineStringInterface
    {
        if (-1 == $index) {
            $index = count($this->lineStrings) - 1;
        }

        /** @var class-string<LineStringInterface> $lineStringClass */
        $lineStringClass = $this->getNamespace().'\LineString';

        return new $lineStringClass($this->lineStrings[$index], $this->srid);
    }

    /**
     * Line strings getter.
     *
     * @return LineStringInterface[]
     */
    public function getLineStrings(): array
    {
        $lineStrings = [];

        for ($i = 0; $i < count($this->lineStrings); ++$i) {
            $lineStrings[] = $this->getLineString($i);
        }

        return $lineStrings;
    }

    /**
     * Type getter.
     *
     * @return string MultiLineString
     */
    public function getType(): string
    {
        return self::MULTILINESTRING;
    }

    /**
     * LineStrings fluent setter.
     *
     * @param ((float|int)[][]|LineStringInterface|MultiPointInterface|PointInterface[])[] $lineStrings array of LineString
     *
     * @throws InvalidValueException when a linestring is not valid
     */
    public function setLineStrings(array $lineStrings): self
    {
        $this->lineStrings = $this->validateMultiLineStringValue($lineStrings);

        return $this;
    }

    /**
     * Implements abstract method to convert line strings into an array.
     *
     * @return (float|int)[][][]
     */
    public function toArray(): array
    {
        return $this->lineStrings;
    }
}
