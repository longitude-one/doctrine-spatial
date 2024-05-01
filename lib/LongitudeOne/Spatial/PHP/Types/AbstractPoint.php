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

use LongitudeOne\Geo\String\Exception\RangeException;
use LongitudeOne\Geo\String\Exception\UnexpectedValueException;
use LongitudeOne\Geo\String\Parser;
use LongitudeOne\Spatial\Exception\InvalidValueException;

/**
 * Abstract point object for POINT spatial types.
 *
 * @see https://stackoverflow.com/questions/7309121/preferred-order-of-writing-latitude-longitude-tuples
 * @see https://docs.geotools.org/latest/userguide/library/referencing/order.html
 */
abstract class AbstractPoint extends AbstractGeometry
{
    /**
     * The X coordinate or the longitude.
     *
     * @var string
     */
    protected $x;

    /**
     * The Y coordinate or the latitude.
     *
     * @var string
     */
    protected $y;

    /**
     * AbstractPoint constructor.
     *
     * @throws InvalidValueException when point is invalid
     */
    public function __construct()
    {
        $argv = $this->validateArguments(func_get_args());

        call_user_func_array([$this, 'construct'], $argv);
    }

    /**
     * Latitude getter.
     *
     * @return string
     */
    public function getLatitude()
    {
        return $this->getY();
    }

    /**
     * Longitude getter.
     *
     * @return string
     */
    public function getLongitude()
    {
        return $this->getX();
    }

    /**
     * Type getter.
     *
     * @return string Point
     */
    public function getType()
    {
        return self::POINT;
    }

    /**
     * X getter. (Longitude getter).
     *
     * @return string
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Y getter. Latitude getter.
     *
     * @return string
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Latitude fluent setter.
     *
     * @param mixed $latitude the new latitude of point
     *
     * @return self
     *
     * @throws InvalidValueException when latitude is not valid
     */
    public function setLatitude($latitude)
    {
        return $this->setY($latitude);
    }

    /**
     * Longitude setter.
     *
     * @param mixed $longitude the new longitude
     *
     * @return self
     *
     * @throws InvalidValueException when longitude is not valid
     */
    public function setLongitude($longitude)
    {
        return $this->setX($longitude);
    }

    /**
     * X setter. (Latitude setter).
     *
     * @param string $x the new X
     *
     * @return self
     *
     * @throws InvalidValueException when x is not valid
     */
    public function setX(string $x)
    {
        $parser = new Parser($x);

        try {
            $this->x = $parser->parse();
        } catch (RangeException|UnexpectedValueException $e) {
            throw new InvalidValueException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        return $this;
    }

    /**
     * Y setter. Longitude Setter.
     *
     * @param string $y the new Y value
     *
     * @return self
     *
     * @throws InvalidValueException when Y is invalid, not in valid range
     */
    public function setY(string $y)
    {
        $parser = new Parser($y);

        try {
            $this->y = $parser->parse();
        } catch (RangeException|UnexpectedValueException $e) {
            throw new InvalidValueException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        return $this;
    }

    /**
     * Convert point into an array X, Y.
     * Latitude, longitude.
     *
     * @return array
     */
    public function toArray()
    {
        return [$this->x, $this->y];
    }

    /**
     * Abstract point constructor.
     *
     * @param int      $x    X, longitude
     * @param int      $y    Y, latitude
     * @param int|null $srid Spatial Reference System Identifier
     *
     * @throws InvalidValueException if x or y are invalid
     */
    protected function construct($x, $y, $srid = null)
    {
        $this->setX($x)
            ->setY($y)
            ->setSrid($srid)
        ;
    }

    /**
     * Validate arguments.
     *
     * @param ?array $argv list of arguments
     *
     * @return array
     *
     * @throws InvalidValueException when an argument is not valid
     */
    protected function validateArguments(?array $argv = null)
    {
        $argc = count($argv);

        if (1 == $argc && is_array($argv[0])) {
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

        array_walk($argv, function (&$value) {
            $tmp = 'Array';
            if (!is_array($value)) {
                $tmp = sprintf('"%s"', $value);
            }
            $value = $tmp;
        });

        throw new InvalidValueException(sprintf(
            'Invalid parameters passed to %s::%s: %s',
            get_class($this),
            '__construct',
            implode(', ', $argv)
        ));
    }
}
