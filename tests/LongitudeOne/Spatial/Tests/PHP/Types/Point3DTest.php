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

namespace LongitudeOne\Spatial\Tests\PHP\Types;

use LongitudeOne\Spatial\PHP\Types\AbstractPoint;
use LongitudeOne\Spatial\PHP\Types\Geography\Point3D as GeographicPoint3D;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point3D as GeometricPoint3D;
use LongitudeOne\Spatial\Tests\Helper\PointHelperTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Point object tests.
 *
 * @group php
 *
 * @internal
 *
 * @coversDefaultClass
 */
class Point3DTest extends TestCase
{
    use PointHelperTrait;

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>}, null, void>
     */
    public static function pointTypeProvider(): \Generator
    {
        yield 'GeometricPoint3D' => [GeometricPoint3D::class];

        yield 'GeographicPoint3D' => [GeographicPoint3D::class];
    }

    /**
     * Test a valid numeric point.
     *
     * @param class-string<GeographicPoint3D|GeometricPoint3D> $pointClassName the point class name
     */
    #[DataProvider('pointTypeProvider')]
    public function testGoodNumericPoint(string $pointClassName): void
    {
        $point = new $pointClassName(34.0522, -118.2430, 0.0);

        static::assertEquals(34.0522, $point->getX());
        static::assertEquals(-118.2430, $point->getY());
        static::assertEquals(0.0, $point->getZ());

        $point->setZ(0);
        static::assertEquals(0, $point->getZ());
    }

    /**
     * Test to convert geographic and geometric 3D points to array.
     *
     * @param class-string<GeographicPoint3D|GeometricPoint3D> $pointClassName the point class name
     */
    #[DataProvider('pointTypeProvider')]
    public function testToArray(string $pointClassName): void
    {
        $point = new $pointClassName(34.0522, -118.2430, 42.0);
        static::assertEquals([34.0522, -118.2430, 42.0], $point->toArray());

        $point = new $pointClassName(34, -118, 42);
        static::assertEquals([34, -118, 42], $point->toArray());
    }

    /**
     * Test to convert geographic and geometric 3D points to string.
     *
     * @param class-string<GeographicPoint3D|GeometricPoint3D> $pointClassName the point class name
     */
    #[DataProvider('pointTypeProvider')]
    public function testToString(string $pointClassName): void
    {
        $point = new $pointClassName(34.0522, -118.2430, 42.5);
        static::assertEquals('34.0522 -118.243 42.5', $point->__toString());
        static::assertEquals('34.0522 -118.243 42.5', (string) $point);

        $point = new $pointClassName(34, -118, 42);
        static::assertEquals('34 -118 42', (string) $point);
    }
}
