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

namespace LongitudeOne\Spatial\Tests\Helper;

use LongitudeOne\Spatial\PHP\Types\Geometry\LineString;
use LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity;

/**
 * LineStringHelperTrait Trait.
 *
 * This helper provides some methods to persist linestring entities.
 *
 * @see /docs/Test.rst
 *
 * @method persistLineString(LineString $linestring)
 */
trait PersistantLineStringHelperTrait
{
    use LineStringHelperTrait;

    /**
     * Create a broken linestring and persist it in database.
     * Line is created with three aligned points: (3 3) (4 15) (5 22).
     */
    protected function persistAngularLineString(): LineStringEntity
    {
        return $this->persistLineString($this->createAngularLineString());
    }

    /**
     * Create a linestring A and persist it in database.
     * Line is created with two points: (0 0, 10 10).
     */
    protected function persistLineStringA(): LineStringEntity
    {
        return $this->persistLineString($this->createLineStringA());
    }

    /**
     * Create a linestring B and persist it in database.
     * Line B crosses lines A and C.
     * Line is created with two points: (0 10, 15 0).
     */
    protected function persistLineStringB(): LineStringEntity
    {
        return $this->persistLineString($this->createLineStringB());
    }

    /**
     * Create a linestring C and persist it in database.
     * Linestring C does not cross linestring A.
     * Linestring C crosses linestring B.
     * Line is created with two points: (2 0, 12 10).
     */
    protected function persistLineStringC(): LineStringEntity
    {
        return $this->persistLineString($this->createLineStringC());
    }

    /**
     * Create a linestring X and persist it in database.
     * Line is created with two points: (8 15, 4 8).
     */
    protected function persistLineStringX(): LineStringEntity
    {
        return $this->persistLineString($this->createLineStringX());
    }

    /**
     * Create a linestring Y and persist it in database.
     * Line is created with two points: (12 14, 3 4).
     */
    protected function persistLineStringY(): LineStringEntity
    {
        return $this->persistLineString($this->createLineStringY());
    }

    /**
     * Create a linestring Z and persist it in database.
     * Line is created with five points: (2 5, 3 6, 12 8, 10 10, 13 11).
     */
    protected function persistLineStringZ(): LineStringEntity
    {
        return $this->persistLineString($this->createLineStringZ());
    }

    /**
     * Create a node linestring and persist it in database.
     * Line is crossing herself like butterfly node: (0 0) (1 0) (0 1) (1 1) (0 0).
     */
    protected function persistNodeLineString(): LineStringEntity
    {
        return $this->persistLineString($this->createNodeLineString());
    }

    /**
     * Create a ring linestring and persist it in database.
     * Line is like a square (0 0, 11) with 4 points: (0 0) (1 0) (1 1) (0 1) (0 0).
     */
    protected function persistRingLineString(): LineStringEntity
    {
        return $this->persistLineString($this->createRingLineString());
    }

    /**
     * Create a straight linestring and persist it in database.
     * Line is created with three aligned points: (0 0) (2 2) (5 5).
     */
    protected function persistStraightLineString(): LineStringEntity
    {
        return $this->persistLineString($this->createStraightLineString());
    }
}
