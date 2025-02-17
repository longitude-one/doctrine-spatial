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

namespace LongitudeOne\Spatial\Tests\Helper;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;

/**
 * LineStringHelperTrait Trait.
 *
 * This helper provides some methods to generate linestring entities.
 *
 * @see /docs/Test.rst
 *
 * @method static never fail(string $message = '')
 */
trait LineStringHelperTrait
{
    /**
     * Create a broken linestring.
     * Line is created with three aligned points: (3 3) (4 15) (5 22).
     */
    protected function createAngularLineString(): LineString
    {
        try {
            return new LineString([
                new Point(3, 3),
                new Point(4, 15),
                new Point(5, 22),
            ]);
        } catch (InvalidValueException $e) {
            self::fail(sprintf('Unable to create angular linestring: %s', $e->getMessage()));
        }
    }

    /**
     * Create a linestring A.
     * Line is created with two points: (0 0, 10 10).
     */
    protected function createLineStringA(): LineString
    {
        try {
            return new LineString([
                new Point(0, 0),
                new Point(10, 10),
            ]);
        } catch (InvalidValueException $e) {
            self::fail(sprintf('Unable to create linestring A (0 0, 10 10): %s', $e->getMessage()));
        }
    }

    /**
     * Create a linestring B.
     * Line B crosses lines A and C.
     * Line is created with two points: (0 10, 15 0).
     */
    protected function createLineStringB(): LineString
    {
        try {
            return new LineString([
                new Point(0, 10),
                new Point(15, 0),
            ]);
        } catch (InvalidValueException $e) {
            self::fail(sprintf('Unable to create linestring B (0 10, 15 0): %s', $e->getMessage()));
        }
    }

    /**
     * Create a linestring C.
     * Linestring C does not cross linestring A.
     * Linestring C crosses linestring B.
     * Line is created with two points: (2 0, 12 10).
     */
    protected function createLineStringC(): LineString
    {
        try {
            return new LineString([
                new Point(2, 0),
                new Point(12, 10),
            ]);
        } catch (InvalidValueException $e) {
            self::fail(sprintf('Unable to create linestring C (2 0, 12 10): %s', $e->getMessage()));
        }
    }

    /**
     * Create a linestring X.
     * Line is created with two points: (8 15, 4 8).
     */
    protected function createLineStringX(): LineString
    {
        try {
            return new LineString([
                new Point(8, 15),
                new Point(4, 8),
            ]);
        } catch (InvalidValueException $e) {
            self::fail(sprintf('Unable to create linestring X (8 15, 4 8): %s', $e->getMessage()));
        }
    }

    /**
     * Create a linestring Y.
     * Line is created with two points: (12 14, 3 4).
     */
    protected function createLineStringY(): LineString
    {
        try {
            return new LineString([
                new Point(12, 14),
                new Point(3, 4),
            ]);
        } catch (InvalidValueException $e) {
            self::fail(sprintf('Unable to create linestring Y (12 14, 3 4): %s', $e->getMessage()));
        }
    }

    /**
     * Create a linestring Z
     * Line is created with five points: (2 5, 3 6, 12 8, 10 10, 13 11).
     */
    protected function createLineStringZ(): LineString
    {
        try {
            return new LineString([
                new Point(2, 5),
                new Point(3, 6),
                new Point(12, 8),
                new Point(10, 10),
                new Point(13, 11),
            ]);
        } catch (InvalidValueException $e) {
            self::fail(sprintf('Unable to create linestring Z: %s', $e->getMessage()));
        }
    }

    /**
     * Create a node linestring.
     * Line is crossing herself like butterfly node: (0 0) (1 0) (0 1) (1 1) (0 0).
     */
    protected function createNodeLineString(): LineString
    {
        try {
            return new LineString([
                new Point(0, 0),
                new Point(1, 0),
                new Point(0, 1),
                new Point(1, 1),
                new Point(0, 0),
            ]);
        } catch (InvalidValueException $e) {
            self::fail(sprintf('Unable to create butterfly node linestring: %s', $e->getMessage()));
        }
    }

    /**
     * Create a ring linestring.
     * Line is like a square (0 0, 1 1) with 4 points: (0 0) (1 0) (1 1) (0 1) (0 0).
     */
    protected function createRingLineString(): LineString
    {
        try {
            return new LineString([
                new Point(0, 0),
                new Point(1, 0),
                new Point(1, 1),
                new Point(0, 1),
                new Point(0, 0),
            ]);
        } catch (InvalidValueException $e) {
            self::fail(sprintf('Unable to create a ring linestring: %s', $e->getMessage()));
        }
    }

    /**
     * Create a straight linestring.
     * Line is created with three aligned points: (0 0) (2 2) (5 5).
     */
    protected function createStraightLineString(): LineString
    {
        try {
            return new LineString([
                new Point(0, 0),
                new Point(2, 2),
                new Point(5, 5),
            ]);
        } catch (InvalidValueException $e) {
            self::fail(sprintf('Unable to create straight linestring: %s', $e->getMessage()));
        }
    }

    /**
     * Create an empty linestring.
     */
    private function createEmptyLineString(): LineString
    {
        try {
            return new LineString([]);
        } catch (InvalidValueException $e) {
            self::fail(sprintf('Unable to create empty linestring: %s', $e->getMessage()));
        }
    }
}
