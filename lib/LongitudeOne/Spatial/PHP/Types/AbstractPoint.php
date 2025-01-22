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

use Doctrine\Deprecations\Deprecation;
use LongitudeOne\Geo\String\Exception\RangeException as GeoParserRangeException;
use LongitudeOne\Geo\String\Exception\UnexpectedValueException;
use LongitudeOne\Geo\String\Parser;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\RangeException;

/**
 * Abstract point object for POINT spatial types.
 *
 * @see https://stackoverflow.com/questions/7309121/preferred-order-of-writing-latitude-longitude-tuples
 * @see https://docs.geotools.org/latest/userguide/library/referencing/order.html
 */
abstract class AbstractPoint extends AbstractGeometry implements PointInterface
{
    private const LINK = 'https://github.com/longitude-one/doctrine-spatial/issues/81';

    /**
     * The X coordinate or the longitude.
     */
    protected float|int $x;

    /**
     * The Y coordinate or the latitude.
     */
    protected float|int $y;

    /**
     * AbstractPoint constructor.
     *
     * @throws InvalidValueException when point is invalid
     */
    public function __construct()
    {
        $argv = $this->validateArguments(func_get_args(), '__construct');

        call_user_func_array([$this, 'construct'], $argv);
    }

    /**
     * This method triggers deprecation messages when developers don't use the next gen constructors.
     *
     * @param mixed[] $argv   arguments passed to the constructor or the calling method
     * @param string  $caller the calling method
     */
    private static function triggerEventualDeprecations(array $argv, string $caller): void
    {
        $argc = count($argv);

        // Array cases
        if (1 === $argc && is_array($argv[0])) {
            Deprecation::trigger(
                'longitude-one/doctrine-spatial',
                self::LINK,
                'Passing an array of coordinates on %s::%s is deprecated since 5.0.2. Please use two arguments instead.',
                static::class,
                $caller
            );

            return;
        }

        if (2 === $argc && is_array($argv[0]) && is_numeric($argv[1])) {
            Deprecation::trigger(
                'longitude-one/doctrine-spatial',
                self::LINK,
                'Passing an array of coordinates and a SRID on %s::%s is deprecated since 5.0.2. Please use three arguments instead.',
                static::class,
                $caller
            );
        }
    }

    /**
     * Latitude getter.
     */
    public function getLatitude(): float|int
    {
        return $this->getY();
    }

    /**
     * Longitude getter.
     */
    public function getLongitude(): float|int
    {
        return $this->getX();
    }

    /**
     * Type getter.
     */
    public function getType(): string
    {
        return self::POINT;
    }

    /**
     * X getter. (Longitude getter).
     */
    public function getX(): float|int
    {
        return $this->x;
    }

    /**
     * Y getter. Latitude getter.
     */
    public function getY(): float|int
    {
        return $this->y;
    }

    /**
     * Latitude fluent setter.
     *
     * @param float|int|string $latitude the new latitude of point
     *
     * @throws InvalidValueException when latitude is not valid
     */
    public function setLatitude(float|int|string $latitude): static
    {
        try {
            $geodesicCoordinate = $this->setGeodesicCoordinate($latitude, -90, 90);
        } catch (RangeException $e) {
            throw new InvalidValueException(sprintf(InvalidValueException::OUT_OF_RANGE_LATITUDE, $latitude), $e->getCode(), $e);
        }

        $this->y = $geodesicCoordinate;

        return $this;
    }

    /**
     * Longitude setter.
     *
     * @param float|int|string $longitude the new longitude
     *
     * @throws InvalidValueException when longitude is not valid
     */
    public function setLongitude(float|int|string $longitude): static
    {
        try {
            $geodesicCoordinate = $this->setGeodesicCoordinate($longitude, -180, 180);
        } catch (RangeException $e) {
            throw new InvalidValueException(sprintf(InvalidValueException::OUT_OF_RANGE_LONGITUDE, $longitude), $e->getCode(), $e);
        }

        $this->x = $geodesicCoordinate;

        return $this;
    }

    /**
     * X setter. (Latitude setter).
     *
     * @param float|int|string $x the new X
     *
     * @throws InvalidValueException when x is not valid
     */
    public function setX(float|int|string $x): static
    {
        $this->x = $this->setCartesianCoordinate($x);

        return $this;
    }

    /**
     * Y setter. Longitude Setter.
     *
     * @param float|int|string $y the new Y value
     *
     * @throws InvalidValueException when Y is invalid, not in valid range
     */
    public function setY(float|int|string $y): static
    {
        $this->y = $this->setCartesianCoordinate($y);

        return $this;
    }

    /**
     * Convert point into an array X, Y.
     * Latitude, longitude.
     *
     * @return array{0 : float|int, 1 : float|int}
     */
    public function toArray(): array
    {
        return [$this->x, $this->y];
    }

    /**
     * Validate arguments.
     *
     * @param mixed[] $argv   list of arguments
     * @param string  $caller the calling method
     *
     * @return (float|int|string)[]
     *
     * @throws InvalidValueException when an argument is not valid
     */
    protected function validateArguments(array $argv, string $caller): array
    {
        self::triggerEventualDeprecations($argv, $caller);

        $argc = count($argv);

        if (1 == $argc && is_array($argv[0])) {
            $count = count($argv[0]);
            if ($count < 2 || $count > 3) {
                throw $this->createException($argv[0], $caller, true);
            }

            foreach ($argv[0] as $value) {
                if (is_numeric($value) || is_string($value)) {
                    continue;
                }

                throw $this->createException($argv[0], $caller, true);
            }

            return $argv[0];
        }

        if (2 == $argc) {
            if (is_array($argv[0]) && (is_numeric($argv[1]) || null === $argv[1] || is_string($argv[1]))) {
                $argv[0][] = $argv[1];

                return $argv[0];
            }

            if ((is_numeric($argv[0]) || is_string($argv[0])) && (is_numeric($argv[1]) || is_string($argv[1]))) {
                return $argv;
            }
        }

        if (3 == $argc) {
            if ((is_numeric($argv[0]) || is_string($argv[0]))
                && (is_numeric($argv[1]) || is_string($argv[1]))
                && (is_numeric($argv[2]) || null === $argv[2] || is_string($argv[2]))
            ) {
                return $argv;
            }
        }

        throw $this->createException($argv, $caller);
    }

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
     * Create a fluent message for InvalidException.
     *
     * @param mixed[] $argv     the arguments
     * @param string  $caller   the method calling the method calling exception :)
     * @param bool    $subArray when the first argument was a subarray converted into an array
     */
    private function createException(array $argv, string $caller, bool $subArray = false): InvalidValueException
    {
        array_walk($argv, function (&$value) {
            if (is_numeric($value) || is_string($value)) {
                return;
            }

            $value = gettype($value);
        });

        $message = 'Invalid parameters passed to %s::%s: %s';
        if ($subArray) {
            $message = 'Invalid parameters passed to %s::%s: array(%s)';
        }

        return new InvalidValueException(sprintf(
            $message,
            static::class,
            $caller,
            implode(', ', $argv)
        ));
    }

    /**
     * Use the longitude-one/geo-parser to parse a coordinate.
     *
     * @param string $coordinate the coordinate to parse
     *
     * @throws InvalidValueException when coordinate is invalid
     */
    private function geoParse(string $coordinate): float|int
    {
        try {
            $parser = new Parser($coordinate);

            $parsedCoordinate = $parser->parse();
        } catch (GeoParserRangeException $e) {
            $message = match ($e->getCode()) {
                GeoParserRangeException::LATITUDE_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_LATITUDE, $coordinate),
                GeoParserRangeException::LONGITUDE_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_LONGITUDE, $coordinate),
                GeoParserRangeException::MINUTES_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_MINUTE, $coordinate),
                GeoParserRangeException::SECONDS_OUT_OF_RANGE => sprintf(InvalidValueException::OUT_OF_RANGE_SECOND, $coordinate),
                default => $e->getMessage(),
            };

            throw new InvalidValueException($message, $e->getCode(), $e);
        } catch (UnexpectedValueException $e) {
            throw new InvalidValueException(sprintf('Invalid coordinate value, got "%s".', $coordinate), $e->getCode(), $e);
        }

        if (is_array($parsedCoordinate)) {
            throw new InvalidValueException('Invalid coordinate value, coordinate cannot be an array.');
        }

        return $parsedCoordinate;
    }

    /**
     * Set a cartesian coordinate.
     * Abscissa or ordinate.
     *
     * @param float|int|string $coordinate the coordinate to set
     *
     * @throws InvalidValueException when coordinate is invalid, RangeException is never thrown
     */
    private function setCartesianCoordinate(float|int|string $coordinate): float|int
    {
        if (is_integer($coordinate) || is_float($coordinate)) {
            // We don't check the range of the value.
            return $coordinate;
        }

        // $y is a string, let's use the geo-parser.
        return $this->geoParse($coordinate);
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

    /**
     * Abstract point internal constructor.
     *
     * @param string   $x    X, longitude
     * @param string   $y    Y, latitude
     * @param null|int $srid Spatial Reference System Identifier
     *
     * @throws InvalidValueException if x or y are invalid
     */
    abstract protected function construct(string $x, string $y, ?int $srid = null): void;
}
