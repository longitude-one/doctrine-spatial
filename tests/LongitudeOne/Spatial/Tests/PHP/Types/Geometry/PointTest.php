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
class PointTest extends TestCase
{
    use PointHelperTrait;

    /**
     * @return \Generator<string, array{0: float|int|string, 1: float|int|string, 2: float|int, 3: float|int}, null, void>
     */
    public static function goodGeodesicCoordinateProvider(): \Generator
    {
        foreach (LoDataProvider::validGeodesicCoordinateProvider() as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * @return \Generator<string, array{0: float|int|string}, null, void>
     */
    public static function outOfRangeLatitudeProvider(): \Generator
    {
        foreach (LoDataProvider::outOfRangeLatitudeProvider() as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * @return \Generator<string, array{0: float|int|string}, null, void>
     */
    public static function outOfRangeLongitudeProvider(): \Generator
    {
        foreach (LoDataProvider::outOfRangeLongitudeProvider() as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * @return \Generator<string, array{0: float|int|string}, null, void>
     */
    public static function tooBigLatitudeProvider(): \Generator
    {
        foreach (LoDataProvider::outOfRangeLatitudeProvider() as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * @return \Generator<string, array{0: float|int|string}, null, void>
     */
    public static function tooBigLongitudeProvider(): \Generator
    {
        yield 'int(-190)' => [-190];

        yield 'float(-180.01)' => [-180.01];

        yield 'string(-190)' => ['-190'];

        yield 'string(-190°)' => ['-190°'];

        yield 'int(190)' => [190];

        yield 'float(180.01)' => [180.01];

        yield 'string(190)' => ['190'];

        yield 'string(190°)' => ['190°'];
    }

    /**
     * Test exception on embedded arrays.
     *
     * @throws InvalidValueException This SHALL happen
     */
    public function testEmbeddedArrays(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid parameters passed to LongitudeOne\\Spatial\\PHP\\Types\\Geometry\\Point::__construct: array');

        new GeometricPoint([[3, []]]);
    }

    /**
     * Test getType method.
     */
    public function testGetType(): void
    {
        $geometricPoint = new GeometricPoint(0, 0);

        static::assertEquals('Point', $geometricPoint->getType());
    }

    #[DataProvider('goodGeodesicCoordinateProvider')]
    public function testGoodGeodesicCoordinate(float|int|string $longitude, float|int|string $latitude, float|int $expectedLongitude, float|int $expectedLatitude): void
    {
        $geographicPoint = new GeometricPoint(0, 0);
        $geographicPoint->setLongitude($longitude);
        $geographicPoint->setLatitude($latitude);

        static::assertSame($expectedLongitude, $geographicPoint->getLongitude());
        static::assertSame($expectedLatitude, $geographicPoint->getLatitude());
    }

    /**
     * Test a valid numeric point.
     */
    public function testGoodNumericPoint(): void
    {
        $point = $this->createLosAngelesGeometry();

        static::assertEquals(34.0522, $point->getLatitude());
        static::assertEquals(-118.2430, $point->getLongitude());

        try {
            $point
                ->setLatitude('32.782778')
                ->setLongitude('-96.803889')
            ;
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to update geometry point: %s', $e->getMessage()));
        }

        static::assertEquals(32.782778, $point->getLatitude());
        static::assertEquals(-96.803889, $point->getLongitude());
    }

    #[DataProvider('outOfRangeLatitudeProvider')]
    public function testOutOfRangeConstructor(float|int|string $latitude): void
    {
        $geometricPoint = new GeometricPoint(0, $latitude);
        static::assertIsNumeric($geometricPoint->getLatitude());
        static::assertNotEmpty($geometricPoint->getLatitude());
    }

    #[DataProvider('outOfRangeLongitudeProvider')]
    public function testOutOfRangeLongitudeConstructor(float|int|string $longitude): void
    {
        $geometricPoint = new GeometricPoint($longitude, 0);
        static::assertIsNumeric($geometricPoint->getLongitude());
        static::assertNotEmpty($geometricPoint->getLongitude());
    }

    #[DataProvider('outOfRangeLatitudeProvider')]
    public function testOutOfRangeSetLatitude(float|int|string $latitude): void
    {
        $point = new GeometricPoint(0, 0);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Out of range latitude value, latitude must be between -90 and 90, got "%s".', $latitude));
        $point->setLatitude($latitude);
    }

    #[DataProvider('outOfRangeLongitudeProvider')]
    public function testOutOfRangeSetLongitude(float|int|string $longitude): void
    {
        $point = new GeometricPoint(0, 0);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Out of range longitude value, longitude must be between -180 and 180, got "%s".', $longitude));
        $point->setLongitude($longitude);
    }

    public function testSetCoordinatesWithBigInteger(): void
    {
        $point = new GeometricPoint(10, 10);
        $point->setX('200');
        static::assertSame(200, $point->getX());

        $point->setY('100');
        static::assertSame(100, $point->getY());

        $point->setX('180.3');
        static::assertSame(180.3, $point->getX());

        $point->setY('-190.3');
        static::assertSame(-190.3, $point->getY());
    }

    /**
     * Test setLatitude with out-of-range values.
     *
     * @throws InvalidValueException it SHALL happen
     */
    #[DataProvider('tooBigLatitudeProvider')]
    public function testSetLatitudeWithBigInteger(float|int|string $latitude): void
    {
        $point = new GeometricPoint(10, 10);

        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage(sprintf('Out of range latitude value, latitude must be between -90 and 90, got "%s".', $latitude));

        $point->setLatitude($latitude);
    }

    /**
     * Test setLongitude with out-of-range values.
     *
     * @throws InvalidValueException it SHALL happen
     */
    #[DataProvider('tooBigLongitudeProvider')]
    public function testSetLongitudeWithBigInteger(float|int|string $longitude): void
    {
        $point = new GeometricPoint(10, 10);

        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage(sprintf('Out of range longitude value, longitude must be between -180 and 180, got "%s".', $longitude));

        $point->setLongitude($longitude);
    }
}
