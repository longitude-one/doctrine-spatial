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

namespace LongitudeOne\Spatial\Tests\PHP\Types\Geography;

use LongitudeOne\Spatial\PHP\Types\Geography\GeographyInterface;
use LongitudeOne\Spatial\PHP\Types\Geography\Polygon;
use LongitudeOne\Spatial\PHP\Types\Geometry\GeometryInterface;
use LongitudeOne\Spatial\PHP\Types\LineStringInterface;
use LongitudeOne\Spatial\PHP\Types\MultiLineStringInterface;
use LongitudeOne\Spatial\PHP\Types\MultiPointInterface;
use LongitudeOne\Spatial\PHP\Types\MultiPolygonInterface;
use LongitudeOne\Spatial\PHP\Types\PointInterface;
use LongitudeOne\Spatial\PHP\Types\PolygonInterface;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;
use PHPUnit\Framework\TestCase;

/**
 * Polygon geographic object tests.
 *
 * @group php
 *
 * @internal
 *
 * @coversDefaultClass
 */
class PolygonTest extends TestCase
{
    /**
     * Test interfaces.
     */
    public function testInterface()
    {
        $polygon = new Polygon([]);

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
}
