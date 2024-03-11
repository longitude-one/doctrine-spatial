<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017 - 2022
 * (c) Longitude One 2020 - 2022
 * (c) 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace LongitudeOne\Spatial\Tests\PHP\Types\Geography;

use LongitudeOne\Spatial\PHP\Types\Geography\GeographyInterface;
use LongitudeOne\Spatial\PHP\Types\Geography\LineString;
use LongitudeOne\Spatial\PHP\Types\Geography\Point;
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
 * LineString geographic object tests.
 *
 * @group php
 *
 * @internal
 *
 * @coversDefaultClass
 */
class LineStringTest extends TestCase
{
    /**
     * Test interfaces.
     */
    public function testInterface()
    {
        $pointOrigin = new Point('113:4:0W', '32:27:0N');
        $pointFinal = new Point('113:4:0W', '32:27:0N');
        $lineString = new LineString([$pointOrigin, $pointFinal]);

        static::assertInstanceOf(SpatialInterface::class, $lineString);
        static::assertInstanceOf(GeographyInterface::class, $lineString);
        static::assertInstanceOf(LineStringInterface::class, $lineString);
        static::assertNotInstanceOf(PointInterface::class, $lineString);
        static::assertNotInstanceOf(PolygonInterface::class, $lineString);
        static::assertNotInstanceOf(MultiPointInterface::class, $lineString);
        static::assertNotInstanceOf(MultiLineStringInterface::class, $lineString);
        static::assertNotInstanceOf(MultiPolygonInterface::class, $lineString);
        static::assertNotInstanceOf(GeometryInterface::class, $lineString);
    }
}
