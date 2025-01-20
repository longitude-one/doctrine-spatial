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

namespace LongitudeOne\Spatial\Tests\PHP\Types;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\CartesianInterface;
use LongitudeOne\Spatial\PHP\Types\GeodeticInterface;
use LongitudeOne\Spatial\PHP\Types\Geography\GeographyInterface;
use LongitudeOne\Spatial\PHP\Types\Geography\LineString as GeographyLineString;
use LongitudeOne\Spatial\PHP\Types\Geography\Point as GeographyPoint;
use LongitudeOne\Spatial\PHP\Types\Geography\Polygon as GeographyPolygon;
use LongitudeOne\Spatial\PHP\Types\Geometry\GeometryInterface;
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString as GeometryLineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\MultiLineString as GeometryMultiLineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\MultiPoint as GeometryMultiPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\MultiPolygon as GeometryMultiPolygon;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point as GeometryPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\Polygon as GeometryPolygon;
use LongitudeOne\Spatial\PHP\Types\LineStringInterface;
use LongitudeOne\Spatial\PHP\Types\MultiLineStringInterface;
use LongitudeOne\Spatial\PHP\Types\MultiPointInterface;
use LongitudeOne\Spatial\PHP\Types\MultiPolygonInterface;
use LongitudeOne\Spatial\PHP\Types\PointInterface;
use LongitudeOne\Spatial\PHP\Types\PolygonInterface;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;
use PHPUnit\Framework\TestCase;

/**
 * To avoid regression, this class tests the interfaces implemented by the classes.
 *
 * @group php
 *
 * @internal
 *
 * @coversNothing
 */
class InterfaceTest extends TestCase
{
    /**
     * Test Geography LineString interface.
     *
     * @throws InvalidValueException This shall not happen
     */
    public function testGeographyLineStringInterface(): void
    {
        $pointOrigin = new GeographyPoint('113:4:0W', '32:27:0N');
        $pointFinal = new GeographyPoint('113:4:0W', '32:27:0N');
        $lineString = new GeographyLineString([$pointOrigin, $pointFinal]);

        static::assertInstanceOf(SpatialInterface::class, $lineString);
        static::assertInstanceOf(GeographyInterface::class, $lineString);
        static::assertInstanceOf(LineStringInterface::class, $lineString);
        static::assertNotInstanceOf(PointInterface::class, $lineString);
        static::assertNotInstanceOf(GeodeticInterface::class, $lineString);
        static::assertNotInstanceOf(CartesianInterface::class, $lineString);
        static::assertNotInstanceOf(PolygonInterface::class, $lineString);
        static::assertNotInstanceOf(MultiPointInterface::class, $lineString);
        static::assertNotInstanceOf(MultiLineStringInterface::class, $lineString);
        static::assertNotInstanceOf(MultiPolygonInterface::class, $lineString);
        static::assertNotInstanceOf(GeometryInterface::class, $lineString);
    }

    /**
     * Geographic point shall implement SpatialTypeInterface, GeographyInterface and PointInterface.
     */
    public function testGeographyPointInterface(): void
    {
        $point = new GeographyPoint('112:4:0W', '33:27:0N');

        static::assertInstanceOf(SpatialInterface::class, $point);
        static::assertInstanceOf(GeographyInterface::class, $point);
        static::assertInstanceOf(PointInterface::class, $point);
        static::assertInstanceOf(GeodeticInterface::class, $point);
        static::assertNotInstanceOf(CartesianInterface::class, $point);
        static::assertNotInstanceOf(LineStringInterface::class, $point);
        static::assertNotInstanceOf(PolygonInterface::class, $point);
        static::assertNotInstanceOf(MultiPointInterface::class, $point);
        static::assertNotInstanceOf(MultiLineStringInterface::class, $point);
        static::assertNotInstanceOf(MultiPolygonInterface::class, $point);
        static::assertNotInstanceOf(GeometryInterface::class, $point);
    }

    /**
     * Geographic polygon shall implement SpatialTypeInterface, GeographyInterface and PolygonInterface.
     *
     * @throws InvalidValueException This shall not happen
     */
    public function testGeographyPolygonInterface(): void
    {
        $polygon = new GeographyPolygon([]);

        static::assertInstanceOf(SpatialInterface::class, $polygon);
        static::assertInstanceOf(GeographyInterface::class, $polygon);
        static::assertInstanceOf(PolygonInterface::class, $polygon);
        static::assertNotInstanceOf(PointInterface::class, $polygon);
        static::assertNotInstanceOf(LineStringInterface::class, $polygon);
        static::assertNotInstanceOf(MultiPointInterface::class, $polygon);
        static::assertNotInstanceOf(MultiLineStringInterface::class, $polygon);
        static::assertNotInstanceOf(MultiPolygonInterface::class, $polygon);
        static::assertNotInstanceOf(GeometryInterface::class, $polygon);
    }

    /**
     * Geometry LineString shall implement SpatialTypeInterface, GeometryInterface and LineStringInterface.
     *
     * @throws InvalidValueException This shall not happen
     */
    public function testGeometryLineStringInterface(): void
    {
        $lineString = new GeometryLineString([]);

        static::assertInstanceOf(SpatialInterface::class, $lineString);
        static::assertInstanceOf(GeometryInterface::class, $lineString);
        static::assertInstanceOf(LineStringInterface::class, $lineString);
        static::assertNotInstanceOf(GeodeticInterface::class, $lineString);
        static::assertNotInstanceOf(CartesianInterface::class, $lineString);
        static::assertNotInstanceOf(PointInterface::class, $lineString);
        static::assertNotInstanceOf(PolygonInterface::class, $lineString);
        static::assertNotInstanceOf(MultiPointInterface::class, $lineString);
        static::assertNotInstanceOf(MultiLineStringInterface::class, $lineString);
        static::assertNotInstanceOf(MultiPolygonInterface::class, $lineString);
        static::assertNotInstanceOf(GeographyInterface::class, $lineString);
    }

    /**
     * Geometry point shall implement SpatialTypeInterface, GeometryInterface and PointInterface.
     *
     * @throws InvalidValueException This shall not happen
     */
    public function testGeometryPointInterface(): void
    {
        $point = new GeometryPoint(4, 2);

        static::assertInstanceOf(SpatialInterface::class, $point);
        static::assertInstanceOf(GeometryInterface::class, $point);
        static::assertInstanceOf(PointInterface::class, $point);
        static::assertInstanceOf(CartesianInterface::class, $point);
        static::assertNotInstanceOf(GeodeticInterface::class, $point);
        static::assertNotInstanceOf(LineStringInterface::class, $point);
        static::assertNotInstanceOf(PolygonInterface::class, $point);
        static::assertNotInstanceOf(MultiPointInterface::class, $point);
        static::assertNotInstanceOf(MultiLineStringInterface::class, $point);
        static::assertNotInstanceOf(MultiPolygonInterface::class, $point);
        static::assertNotInstanceOf(GeographyInterface::class, $point);
    }

    /**
     * Geometry polygon shall implement SpatialTypeInterface, GeometryInterface and PolygonInterface.
     *
     * @throws InvalidValueException This shall not happen
     */
    public function testGeometryPolygonInterface(): void
    {
        $polygon = new GeometryPolygon([]);

        static::assertInstanceOf(SpatialInterface::class, $polygon);
        static::assertInstanceOf(GeometryInterface::class, $polygon);
        static::assertInstanceOf(PolygonInterface::class, $polygon);
        static::assertNotInstanceOf(PointInterface::class, $polygon);
        static::assertNotInstanceOf(LineStringInterface::class, $polygon);
        static::assertNotInstanceOf(MultiPointInterface::class, $polygon);
        static::assertNotInstanceOf(MultiLineStringInterface::class, $polygon);
        static::assertNotInstanceOf(MultiPolygonInterface::class, $polygon);
        static::assertNotInstanceOf(GeographyInterface::class, $polygon);
    }

    /**
     * MultiPolygon shall implement SpatialTypeInterface, GeometryInterface and MultiPolygonInterface.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testInterface(): void
    {
        $multiPolygon = new GeometryMultiPolygon([]);

        static::assertInstanceOf(SpatialInterface::class, $multiPolygon);
        static::assertInstanceOf(GeometryInterface::class, $multiPolygon);
        static::assertInstanceOf(MultiPolygonInterface::class, $multiPolygon);
        static::assertNotInstanceOf(PointInterface::class, $multiPolygon);
        static::assertNotInstanceOf(LineStringInterface::class, $multiPolygon);
        static::assertNotInstanceOf(PolygonInterface::class, $multiPolygon);
        static::assertNotInstanceOf(MultiLineStringInterface::class, $multiPolygon);
        static::assertNotInstanceOf(MultiPointInterface::class, $multiPolygon);
        static::assertNotInstanceOf(GeographyInterface::class, $multiPolygon);
    }

    /**
     * MultiLineString shall implement SpatialTypeInterface, GeometryInterface and MultiLineStringInterface.
     *
     * @throws InvalidValueException This shall not happen
     */
    public function testMultiLineStringInterface(): void
    {
        $multiLineString = new GeometryMultiLineString([]);

        static::assertInstanceOf(SpatialInterface::class, $multiLineString);
        static::assertInstanceOf(GeometryInterface::class, $multiLineString);
        static::assertInstanceOf(MultiLineStringInterface::class, $multiLineString);
        static::assertNotInstanceOf(PointInterface::class, $multiLineString);
        static::assertNotInstanceOf(LineStringInterface::class, $multiLineString);
        static::assertNotInstanceOf(PolygonInterface::class, $multiLineString);
        static::assertNotInstanceOf(MultiPointInterface::class, $multiLineString);
        static::assertNotInstanceOf(MultiPolygonInterface::class, $multiLineString);
        static::assertNotInstanceOf(GeographyInterface::class, $multiLineString);
    }

    /**
     * MultiPoint shall implement SpatialTypeInterface, GeometryInterface and MultiPointInterface.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiPointInterface(): void
    {
        $multiPoint = new GeometryMultiPoint([]);

        static::assertInstanceOf(SpatialInterface::class, $multiPoint);
        static::assertInstanceOf(GeometryInterface::class, $multiPoint);
        static::assertInstanceOf(MultiPointInterface::class, $multiPoint);
        static::assertNotInstanceOf(PointInterface::class, $multiPoint);
        static::assertNotInstanceOf(LineStringInterface::class, $multiPoint);
        static::assertNotInstanceOf(PolygonInterface::class, $multiPoint);
        static::assertNotInstanceOf(MultiLineStringInterface::class, $multiPoint);
        static::assertNotInstanceOf(MultiPolygonInterface::class, $multiPoint);
        static::assertNotInstanceOf(GeographyInterface::class, $multiPoint);
    }
}
