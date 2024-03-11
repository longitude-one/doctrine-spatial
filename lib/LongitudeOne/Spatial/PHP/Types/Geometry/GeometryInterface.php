<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2024
 * Copyright Longitude One 2020-2024
 * Copyright 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace LongitudeOne\Spatial\PHP\Types\Geometry;

/**
 * Geometry interface for Geometry objects.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 */
interface GeometryInterface
{
    public const GEOMETRY = 'Geometry';
    public const GEOMETRYCOLLECTION = 'GeometryCollection';
    public const LINESTRING = 'LineString';
    public const MULTILINESTRING = 'MultiLineString';
    public const MULTIPOINT = 'MultiPoint';
    public const MULTIPOLYGON = 'MultiPolygon';
    public const POINT = 'Point';
    public const POLYGON = 'Polygon';

    /**
     * @return string
     */
    public function getType();
}
