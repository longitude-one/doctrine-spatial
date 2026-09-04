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

namespace LongitudeOne\Spatial\Tests\PHP\Types;

use LongitudeOne\Spatial\PHP\Types\AbstractPoint;
use LongitudeOne\Spatial\PHP\Types\Geography\Point as GeographicPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point as GeometricPoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Public value representations and spatial metadata shared by point types.
 *
 * @internal
 */
#[CoversClass(AbstractPoint::class)]
#[CoversClass(GeographicPoint::class)]
#[CoversClass(GeometricPoint::class)]
#[Group('php')]
class AbstractPointRepresentationTest extends AbstractPointTestCase
{
    // phpcs:disable Squiz.Commenting.FunctionComment.IncorrectTypeHint

    /**
     * Test getType method.
     *
     * @param class-string<AbstractPoint> $class the classname to test, Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testGetType(string $class): void
    {
        $point = new $class(0, 0);

        static::assertSame('Point', $point->getType());
    }

    /**
     * Test JSON serialization with and without an SRID.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testJson(string $abstractPoint): void
    {
        $point = new $abstractPoint(5, 5);

        static::assertSame('{"type":"Point","coordinates":[5,5],"srid":null}', $point->toJson());
        static::assertSame('{"type":"Point","coordinates":[5,5],"srid":null}', json_encode($point));

        $point->setSrid(4326);
        static::assertSame('{"type":"Point","coordinates":[5,5],"srid":4326}', $point->toJson());
        static::assertSame('{"type":"Point","coordinates":[5,5],"srid":4326}', json_encode($point));
    }

    /**
     * Test a point created with an array can be converted to a WKT coordinate pair.
     *
     * @param class-string<AbstractPoint> $pointType Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testPointFromArrayToString(string $pointType): void
    {
        $point = new $pointType([5, 5]);

        static::assertSame('5 5', (string) $point);
    }

    /**
     * Test point SRID construction and mutation.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testPointWithSrid(string $abstractPoint): void
    {
        $point = new $abstractPoint(5, 5, 2154);
        static::assertSame(2154, $point->getSrid());

        $point->setSrid(4326);
        static::assertSame(4326, $point->getSrid());
    }

    /**
     * Test conversion of points to their coordinate arrays.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testToArray(string $abstractPoint): void
    {
        $point = new $abstractPoint(-10, 11);
        static::assertSame([-10, 11], $point->toArray());

        $point = new $abstractPoint(-42.42, 42.43);
        static::assertSame([-42.42, 42.43], $point->toArray());
    }
}
