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

namespace LongitudeOne\Spatial\Tests\PHP\Types\Geometry;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point as GeometricPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point3D;
use LongitudeOne\Spatial\Tests\DataProvider as LoDataProvider;
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
     * Test a valid numeric point.
     */
    public function testGoodNumericPoint(): void
    {
        $point = new Point3D(34.0522, -118.2430, 0.0);

        static::assertEquals(34.0522, $point->getX());
        static::assertEquals(-118.2430, $point->getY());
        static::assertEquals(0.0, $point->getZ());

        $point->setZ(0);
        static::assertEquals(0, $point->getZ());
    }

    public function testToString(): void
    {
        $point = new Point3D(34.0522, -118.2430, 42.5);
        static::assertEquals('PointZ(34.0522 -118.243 42.5)', $point->__toString());

        $point = new Point3D(34, -118, 42);
        static::assertEquals('PointZ(34 -118 42)', $point->__toString());
    }

    public function testToArray(): void
    {
        $point = new Point3D(34.0522, -118.2430, 42.0);
        static::assertEquals([34.0522, -118.2430, 42.0], $point->toArray());

        $point = new Point3D(34, -118, 42);
        static::assertEquals([34, -118, 42], $point->toArray());
    }
}
