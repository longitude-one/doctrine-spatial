<?php
/**
 * This file is part of the Doctrine Spatial extension.
 *
 * PHP 8.4 | 8.5
 * Doctrine ORM ^3.6
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2026
 * Copyright Longitude One 2020-2026
 * Copyright 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace LongitudeOne\Spatial\PHP\Types;

use Doctrine\DBAL\Types\Type;

interface SpatialInterface
{
    public const GEOGRAPHY = 'Geography';
    public const GEOGRAPHYCOLLECTION = 'GeographyCollection';
    public const GEOMETRY = 'Geometry';
    public const GEOMETRYCOLLECTION = 'GeometryCollection';
    public const LINESTRING = 'LineString';
    public const MULTILINESTRING = 'MultiLineString';
    public const MULTIPOINT = 'MultiPoint';
    public const MULTIPOLYGON = 'MultiPolygon';
    public const POINT = 'Point';
    public const POLYGON = 'Polygon';

    /**
     * Return the Spatial Reference Identifier (SRID) of this object.
     */
    public function getSrid(): ?int;

    /**
     * Return the type of this geometry or geography.
     * This function is used by the spatial type to get the type of the object.
     */
    public function getType(): string;

    /**
     * Set the Spatial Reference Identifier (SRID) of this object.
     *
     * @param ?int $srid the Spatial Reference Identifier (SRID)
     */
    public function setSrid(?int $srid): self;

    /**
     * Convert any spatial object to its array representation.
     *
     * Array does NOT contain SpatialInterface, only floats, integers, and arrays.
     *
     * @return (float|int)[]|(float|int)[][]|(float|int)[][][]|(float|int)[][][][]
     */
    public function toArray(): array;

    /**
     * Convert any spatial object to its string representation.
     * Example: 'POINT(42 42)'.
     */
    public function __toString(): string;
}
