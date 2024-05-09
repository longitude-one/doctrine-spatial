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
 * Abstract geometry object for spatial types.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 */
abstract class AbstractGeometry implements \JsonSerializable, SpatialInterface
{
    /**
     * Spatial Reference System Identifier.
     *
     * @var int
     */
    protected $srid;

    /**
     * Spatial Reference System Identifier getter.
     *
     * @return null|int
     */
    public function getSrid()
    {
        return $this->srid;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @see https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @see https://github.com/creof/doctrine-spatial/issues/140
     *
     * @return array{type: string, coordinates: array<int, mixed>, srid: ?int} data which can be serialized by <b>json_encode</b>,
     *                                                                         which is a value of any type other than a resource
     *
     * @since 2.0.0.rc-1
     */
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->getType(),
            'coordinates' => $this->toArray(),
            'srid' => $this->getSrid(),
        ];
    }

    /**
     * Spatial Reference System Identifier fluent setter.
     *
     * @param mixed $srid Spatial Reference System Identifier
     *
     * @return self
     */
    public function setSrid($srid)
    {
        if (null !== $srid) {
            $this->srid = (int) $srid;
        }

        return $this;
    }

    /**
     * Convert this abstract geometry to a Json string.
     *
     * @return string
     */
    public function toJson()
    {
        $json = [];
        $json['type'] = $this->getType();
        $json['coordinates'] = $this->toArray();
        $json['srid'] = $this->getSrid();

        $json = json_encode($json);

        if (false === $json) {
            // IMO, it could only happen if someone sends a resource as coordinates
            throw new InvalidValueException('Cannot convert geometry to JSON string');
        }

        return $json;
    }

    /**
     * Return the namespace of this class.
     *
     * @return string
     */
    protected function getNamespace()
    {
        $class = get_class($this);

        return mb_substr($class, 0, mb_strrpos($class, '\\') - mb_strlen($class));
    }

    /**
     * Validate line strings value.
     *
     * @param (float|int)[][]|LineStringInterface|MultiPointInterface|PointInterface[] $lineString line string to validate
     *
     * @return (float|int)[][]
     *
     * @throws InvalidValueException when a point of line string is not valid
     */
    protected function validateLineStringValue($lineString)
    {
        return $this->validateMultiPointValue($lineString);
    }

    /**
     * Validate multiline strings value.
     *
     * @param ((float|int)[][]|LineStringInterface|MultiPointInterface|PointInterface[])[] $lineStrings the array of line strings to validate
     *
     * @return (float|int)[][][]
     *
     * @throws InvalidValueException as soon as a point of a line string is not valid
     */
    protected function validateMultiLineStringValue(array $lineStrings)
    {
        /** @var (float|int)[][][] $result */
        $result = [];
        foreach ($lineStrings as $lineString) {
            $result[] = $this->validateLineStringValue($lineString);
        }

        return $result;
    }

    /**
     * Validate multi point value.
     *
     * @param (float|int)[][]|LineStringInterface|MultiPointInterface|PointInterface[] $points array of geometric data to validate
     *
     * @return (float|int)[][]
     *
     * @throws InvalidValueException when one point is not valid
     */
    protected function validateMultiPointValue($points)
    {
        /** @var (float|int)[][] $result */
        $result = [];
        if ($points instanceof SpatialInterface) {
            $result = $points->toArray();
        }

        if (is_array($points)) {
            foreach ($points as $point) {
                $result[] = $this->validatePointValue($point);
            }
        }

        return $result;
    }

    /**
     * Validate multi polygon value.
     *
     * @param ((float|int)[][][]|LineStringInterface[]|MultiPointInterface[]|PointInterface[][]|PolygonInterface)[] $polygons the array of polygons to validate
     *
     * @return ((float|int)[][][]|LineStringInterface[]|MultiPointInterface[]|PointInterface[][]|PolygonInterface)[] the validated polygons
     *
     * @throws InvalidValueException when one polygon is not valid
     */
    protected function validateMultiPolygonValue(array $polygons)
    {
        $result = [];
        foreach ($polygons as $polygon) {
            /** @var (float|int)[][][]|LineStringInterface[]|MultiPointInterface[]|PointInterface[][] $polygonArray */
            $polygonArray = $polygon instanceof PolygonInterface ? $polygon->toArray() : $polygon;

            $result[] = $this->validatePolygonValue($polygonArray);
        }

        return $result;
    }

    /**
     * Validate a geometric point or an array of geometric points.
     *
     * @param (float|int)[]|PointInterface $point the geometric point(s) to validate
     *
     * @return (float|int)[]
     *
     * @throws InvalidValueException as soon as one point is not valid
     */
    protected function validatePointValue($point): array
    {
        switch (true) {
            case $point instanceof PointInterface:
                return $point->toArray();

            case is_array($point) && 2 == count($point) && is_numeric($point[0]) && is_numeric($point[1]):
                return array_values($point);

            default:
                throw new InvalidValueException(sprintf(
                    'Invalid %s Point value of type "%s"',
                    $this->getType(),
                    is_object($point) ? get_class($point) : gettype($point)
                ));
        }
    }

    /**
     * Validate polygon values.
     *
     * @param ((float|int)[][]|LineStringInterface|MultiPointInterface|PointInterface[])[] $polygon the array of rings
     *
     * @return (float|int)[][][] the validated rings
     *
     * @throws InvalidValueException when ring is not valid
     */
    protected function validatePolygonValue(array $polygon)
    {
        $result = [];
        foreach ($polygon as $lineString) {
            $result[] = $this->validateRingValue($lineString);
        }

        return $result;
    }

    /**
     * Validate ring value.
     *
     * @param (float|int)[][]|LineStringInterface|MultiPointInterface|PointInterface[] $ring the ring or a ring converted to array
     *
     * @return (float|int)[][] the validate ring
     *
     * @throws InvalidValueException when the ring is not an abstract line string or is not closed
     */
    protected function validateRingValue($ring)
    {
        if ($ring instanceof MultiPointInterface || $ring instanceof LineStringInterface) {
            $ring = $ring->toArray();
        }

        if (!is_array($ring)) {
            throw new InvalidValueException(sprintf(
                'Invalid %s LineString value of type "%s"',
                $this->getType(),
                is_object($ring) ? get_class($ring) : gettype($ring)
            ));
        }

        /** @var (float|int)[][] $points */
        $points = [];
        foreach ($ring as $point) {
            $points[] = $this->validatePointValue($point);
        }

        if ($points[0] !== end($points)) {
            throw new InvalidValueException(sprintf(
                'Invalid polygon, ring "(%s)" is not closed',
                $this->toStringLineString($points)
            ));
        }

        return $points;
    }

    /**
     * Convert a line to string.
     *
     * @param (float|int)[][] $lineString line string already converted into an array
     *
     * @return string
     */
    private function toStringLineString(array $lineString)
    {
        return $this->toStringMultiPoint($lineString);
    }

    /**
     * Convert multiline strings to a string value.
     *
     * @param (float|int)[][][] $multiLineString multi line already converted into an array of coordinates
     *
     * @return string
     */
    private function toStringMultiLineString(array $multiLineString)
    {
        $strings = null;

        foreach ($multiLineString as $lineString) {
            $strings[] = '('.$this->toStringLineString($lineString).')';
        }

        return implode(',', $strings);
    }

    /**
     * Convert multi points to a string value.
     *
     * @param (float|int)[][] $multiPoint multipoint already converted into an array of point
     *
     * @return string
     */
    private function toStringMultiPoint(array $multiPoint)
    {
        $strings = [];

        foreach ($multiPoint as $point) {
            $strings[] = $this->toStringPoint($point);
        }

        return implode(',', $strings);
    }

    /**
     * Convert multipolygon to a string.
     *
     * THIS IS NOT A NON-USED PRIVATE METHOD.
     *
     * @param (float|int)[][][][] $multiPolygon multipolygon already converted into an array of polygon
     *
     * @return string
     */
    private function toStringMultiPolygon(array $multiPolygon)
    {
        $strings = null;

        foreach ($multiPolygon as $polygon) {
            $strings[] = '('.$this->toStringPolygon($polygon).')';
        }

        return implode(',', $strings);
    }

    /**
     * Convert a point to a string value.
     *
     * @param (float|int)[] $point point already converted into an array of TWO coordinates
     *
     * @return string
     */
    private function toStringPoint(array $point)
    {
        return vsprintf('%s %s', $point);
    }

    /**
     * Convert a polygon into a string value.
     *
     * @param (float|int)[][][] $polygon polygons already converted into array
     *
     * @return string
     */
    private function toStringPolygon(array $polygon)
    {
        return $this->toStringMultiLineString($polygon);
    }

    /**
     * Type of this geometry: Linestring, point, etc.
     *
     * @return string
     */
    abstract public function getType();

    /**
     * Convert this abstract geometry to an array.
     *
     * @return (float|int)[]|(float|int)[][]|(float|int)[][][]|(float|int)[][][][]
     */
    abstract public function toArray();

    /**
     * Magic method: convert geometry to string.
     *
     * @return string
     */
    public function __toString()
    {
        $type = mb_strtoupper($this->getType());
        $method = 'toString'.$type;

        return $this->{$method}($this->toArray());
    }
}
