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

namespace LongitudeOne\Spatial\PHP\Types\Geography;

use LongitudeOne\Geo\String\Exception\RangeException;
use LongitudeOne\Geo\String\Exception\UnexpectedValueException;
use LongitudeOne\Geo\String\Parser;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\AbstractPoint;
use LongitudeOne\Spatial\PHP\Types\PointInterface;

/**
 * Point object for POINT geography type.
 */
class Point extends AbstractPoint implements GeographyInterface, PointInterface
{
    /**
     * X setter.
     *
     * todo force string in version 5
     *
     * @param string $x X coordinate
     *
     * @return self
     *
     * @throws InvalidValueException when y is not in range of accepted value, or is totally invalid
     */
    public function setX($x)
    {
        if (!is_string($x)) {
            trigger_deprecation(
                'longitude-one/doctrine-spatial',
                '4.1',
                'Passing a non-string value to %s is deprecated, pass a string instead.',
                __METHOD__
            );
        }

        $parser = new Parser((string) $x);

        try {
            // TODO use a string in next major version
            $x = (float) $parser->parse();
        } catch (RangeException|UnexpectedValueException $e) {
            throw new InvalidValueException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        if ($x < -180 || $x > 180) {
            throw new InvalidValueException(sprintf('Invalid longitude value "%s", must be in range -180 to 180.', $x));
        }

        $this->x = $x;

        return $this;
    }

    /**
     * Y setter.
     *
     * @param mixed $y the Y coordinate
     *
     * @return self
     *
     * @throws InvalidValueException when y is not in range of accepted value, or is totally invalid
     */
    public function setY($y)
    {
        if (!is_string($y)) {
            trigger_deprecation(
                'longitude-one/doctrine-spatial',
                '4.1',
                'Passing a non-string value to %s is deprecated, pass a string instead.',
                __METHOD__
            );
        }

        $parser = new Parser((string) $y);

        try {
            // TODO use a string in next major version
            $y = (float) $parser->parse();
        } catch (RangeException|UnexpectedValueException $e) {
            throw new InvalidValueException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        if ($y < -90 || $y > 90) {
            throw new InvalidValueException(sprintf('Invalid latitude value "%s", must be in range -90 to 90.', $y));
        }

        $this->y = $y;

        return $this;
    }
}
