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

use LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity;

/**
 * PolygonHelperTrait Trait.
 *
 * This helper provides some methods to persist polygons.
 *
 * @see /docs/Test.rst
 *
 * @internal
 */
trait PersistantPolygonHelperTrait
{
    use PersistHelperTrait;
    use PolygonHelperTrait;

    /**
     * Create the BIG Polygon and persist it in database.
     * Square (0 0, 10 10).
     */
    protected function persistBigPolygon(): PolygonEntity
    {
        return $this->persistPolygon($this->createBigPolygon());
    }

    /**
     * Create an eccentric polygon and persist it in database.
     * Square (6 6, 10 10).
     *
     * DO NOT REMOVE THIS UNUSED method, it will be used soon.
     */
    protected function persistEccentricPolygon(): PolygonEntity
    {
        return $this->persistPolygon($this->createEccentricPolygon());
    }

    /**
     * Create the HOLEY Polygon and persist it in database.
     * (Big polygon minus Small Polygon).
     */
    protected function persistHoleyPolygon(): PolygonEntity
    {
        return $this->persistPolygon($this->createHoleyPolygon());
    }

    /**
     * Create the Massachusetts state plane US feet geometry and persist it in database.
     *
     * @param bool $forwardSrid forward SRID for creation
     */
    protected function persistMassachusettsState(bool $forwardSrid = true): PolygonEntity
    {
        return $this->persistPolygon($this->createMassachusettsState($forwardSrid));
    }

    /**
     * Create the Outer Polygon and persist it in database.
     * Square (15 15, 17 17).
     */
    protected function persistOuterPolygon(): PolygonEntity
    {
        return $this->persistPolygon($this->createOuterPolygon());
    }

    /**
     * Create the W Polygon and persist it in database.
     */
    protected function persistPolygonW(): PolygonEntity
    {
        return $this->persistPolygon($this->createPolygonW());
    }

    /**
     * Create the SMALL Polygon and persist it in database.
     * SQUARE (5 5, 7 7).
     */
    protected function persistSmallPolygon(): PolygonEntity
    {
        return $this->persistPolygon($this->createSmallPolygon());
    }
}
