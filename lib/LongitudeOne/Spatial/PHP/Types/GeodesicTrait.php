<?php

namespace LongitudeOne\Spatial\PHP\Types;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\RangeException;

/**
 * Geodesic trait.
 *
 * @internal
 */
trait GeodesicTrait
{
    use GeoParseTrait;

    /**
     * Check the range of a coordinate.
     *
     * @param float|int $coordinate the coordinate to check
     * @param int       $min        the minimum accepted value
     * @param int       $max        the maximum accepted value
     *
     * @return float|int $coordinate or throw a RangeException
     *
     * @throws RangeException when coordinate is out of range fixed by min and max
     */
    private function checkRange(float|int $coordinate, int $min, int $max): float|int
    {
        if ($coordinate < $min || $coordinate > $max) {
            throw new RangeException(sprintf('Coordinate must be comprised between %d and %d, got "%s".', $min, $max, $coordinate));
        }

        return $coordinate;
    }

    /**
     * Set a geodesic coordinate.
     * Latitude or longitude.
     *
     * @param float|int|string $coordinate the coordinate to set
     * @param int              $min        the minimum value
     * @param int              $max        the maximum value
     *
     * @throws InvalidValueException|RangeException when coordinate is invalid or out of range
     */
    private function setGeodesicCoordinate(float|int|string $coordinate, int $min, int $max): float|int
    {
        if (is_integer($coordinate) || is_float($coordinate)) {
            // We check the range of the value.
            return $this->checkRange($coordinate, $min, $max);
        }

        // $y is a string, let's use the geo-parser.
        $parsedCoordinate = $this->geoParse($coordinate);

        if ($parsedCoordinate < $min || $parsedCoordinate > $max) {
            throw new RangeException(sprintf('Coordinate must be comprised between %d and %d, got "%s".', $min, $max, $coordinate));
        }

        return $parsedCoordinate;
    }
}