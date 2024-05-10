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
     * @param string $x X coordinate
     *
     * @throws InvalidValueException when y is not in range of accepted value, or is totally invalid
     */
    public function setX(string $x): self
    {
        $parser = new Parser($x);

        try {
            $x = $parser->parse();
        } catch (RangeException $e) {
            throw new InvalidValueException($e->getMessage(), $e->getCode(), $e);
        } catch (UnexpectedValueException $e) {
            throw new InvalidValueException(sprintf('Invalid longitude value, got "%s".', $x), $e->getCode(), $e);
        }

        if (is_array($x)) {
            throw new InvalidValueException('Invalid longitude value, longitude cannot be an array.');
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
     * @param string $y the Y coordinate
     *
     * @throws InvalidValueException when y is not in range of accepted value, or is totally invalid
     */
    public function setY(string $y): self
    {
        $parser = new Parser($y);

        try {
            $y = $parser->parse();
        } catch (RangeException $e) {
            throw new InvalidValueException($e->getMessage(), $e->getCode(), $e);
        } catch (UnexpectedValueException $e) {
            throw new InvalidValueException(sprintf('Invalid latitude value, got "%s".', $y), $e->getCode(), $e);
        }

        if (is_array($y)) {
            throw new InvalidValueException('Invalid latitude value, latitude cannot be an array.');
        }

        if ($y < -90 || $y > 90) {
            throw new InvalidValueException(sprintf('Invalid latitude value "%s", must be in range -90 to 90.', $y));
        }

        $this->y = $y;

        return $this;
    }
}
