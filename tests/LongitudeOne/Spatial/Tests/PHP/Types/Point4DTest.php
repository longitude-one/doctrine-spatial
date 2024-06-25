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
use LongitudeOne\Spatial\PHP\Types\Geography\Point4D as GeographicPoint4D;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point4D as GeometricPoint4D;
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
class Point4DTest extends TestCase
{
    use PointHelperTrait;

    public const DATE_TIME_STRING = '2017-06-23 01:02:03.123';
    public const DATE_TIME_TIMESTAMP = 1498179723;

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>}, null, void>
     */
    public static function pointTypeProvider(): \Generator
    {
        yield 'GeometricPoint4D' => [GeometricPoint4D::class];

        yield 'GeographicPoint4D' => [GeographicPoint4D::class];
    }

    /**
     * Test a valid numeric point.
     *
     * @param class-string<GeographicPoint4D|GeometricPoint4D> $pointClassName the point class name
     */
    #[DataProvider('pointTypeProvider')]
    public function testSetters(string $pointClassName): void
    {
        $point = new $pointClassName(34.0522, -118.2430, 0.0, new \DateTime(self::DATE_TIME_STRING));

        static::assertSame(34.0522, $point->getX());
        static::assertSame(-118.2430, $point->getY());
        static::assertSame(0.0, $point->getZ());
        static::assertEquals(new \DateTime(self::DATE_TIME_STRING), $point->getMoment());

        $actual = $expected = new \DateTime('2017-10-22 01:02:03.000');
        $point->setMoment($actual);
        static::assertSame($expected, $point->getMoment());
    }

    /**
     * Test to convert geographic and geometric 4D points to array.
     *
     * @param class-string<GeographicPoint4D|GeometricPoint4D> $pointClassName the point class name
     */
    #[DataProvider('pointTypeProvider')]
    public function testToArray(string $pointClassName): void
    {
        $point = new $pointClassName(34.0522, -118.2430, 42.0, new \DateTime(self::DATE_TIME_STRING));
        static::assertSame([34.0522, -118.2430, 42.0, self::DATE_TIME_TIMESTAMP], $point->toArray());

        $point = new $pointClassName(34, -118, 42, new \DateTime(self::DATE_TIME_STRING));
        static::assertSame([34, -118, 42, self::DATE_TIME_TIMESTAMP], $point->toArray());
    }

    /**
     * Test to convert geographic and geometric 4D points to string.
     *
     * @param class-string<GeographicPoint4D|GeometricPoint4D> $pointClassName the point class name
     */
    #[DataProvider('pointTypeProvider')]
    public function testToString(string $pointClassName): void
    {
        $point = new $pointClassName(34.0522, -118.2430, 42.5, new \DateTime(self::DATE_TIME_STRING));
        static::assertSame('34.0522 -118.243 42.5 '.self::DATE_TIME_TIMESTAMP, $point->__toString());
        static::assertSame('34.0522 -118.243 42.5 '.self::DATE_TIME_TIMESTAMP, (string) $point);

        $point = new $pointClassName(34, -118, 42, new \DateTime(self::DATE_TIME_STRING));
        static::assertSame('34 -118 42 '.self::DATE_TIME_TIMESTAMP, (string) $point);
    }
}
