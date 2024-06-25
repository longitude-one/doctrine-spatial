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

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\AbstractPoint;
use LongitudeOne\Spatial\PHP\Types\Geography\Point as GeographicPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point as GeometricPoint;
use LongitudeOne\Spatial\Tests\DataProvider as LoDataProvider;
use LongitudeOne\Spatial\Tests\Helper\PointHelperTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Geometric and geographic points tests.
 * These methods involve tests launched on both Geometric and Geographic points.
 *
 * @group php
 *
 * @internal
 *
 * @covers \LongitudeOne\Spatial\PHP\Types\AbstractPoint
 * @covers \LongitudeOne\Spatial\PHP\Types\Geography\Point
 * @covers \LongitudeOne\Spatial\PHP\Types\Geometry\Point
 */
class AbstractPointTest extends TestCase
{
    // phpcs:disable Squiz.Commenting.FunctionComment.IncorrectTypeHint

    use PointHelperTrait;

    private const DELTA = 0.000000000001;

    /**
     * @return \Generator<string, array{0: float|int|string}, null, void>
     */
    public static function easyValuesProvider(): \Generator
    {
        $points = [
            'GeometricPoint' => GeometricPoint::class,
            'GeographicPoint' => GeographicPoint::class,
        ];

        $methods = [
            'setX' => ['getX', 'getLongitude'],
            'setY' => ['getY', 'getLatitude'],
            'setLongitude' => ['getX', 'getLongitude'],
            'setLatitude' => ['getY', 'getLatitude'],
        ];

        $values = [
            'int(20)' => ['actual' => 20, 'expected' => 20],
            'float(20.0)' => ['actual' => 20.0, 'expected' => 20.0],
            'string(20)' => ['actual' => '20', 'expected' => 20],
            'string(20.0)' => ['actual' => '20.0', 'expected' => 20],
        ];

        foreach ($points as $className => $class) {
            foreach ($methods as $setter => $getters) {
                foreach ($values as $valueName => $value) {
                    yield sprintf('%s with %s and %s', $className, $setter, $valueName) => [$class, $setter, $getters, $value['actual'], $value['expected']];
                }
            }
        }
    }

    /**
     * @return \Generator<string, array{0: class-string, 1: float|int|string, 2: float|int|string, 3: float|int, 4: float|int}, null, void>
     */
    public static function mixedGeodesicCoordinateProvider(): \Generator
    {
        $points = [
            'GeometricPoint' => GeometricPoint::class,
            'GeographicPoint' => GeographicPoint::class,
        ];

        /** @var array<string, array{0: float, 1: float}> $geodesicCoordinates */
        $geodesicCoordinates = [
            '79:56:55W, 40:26:46N' => [-79.9486111111111, 40.44611111111111],
            '79° 56\' 55" W, 40° 26\' 46" N' => [-79.9486111111111, 40.44611111111111],
            '79°56′55″W, 40°26′46″N' => [-79.9486111111111, 40.44611111111111],
            '79° 56′ 55″ W, 40° 26′ 46″ N' => [-79.9486111111111, 40.44611111111111],
            '79:56:55.832W, 40:26:46.543N' => [-79.94884222222223, 40.446261944444444],
            '112:4:0W, 33:27:0N' => [-112.066666666666, 33.45],
        ];

        foreach ($points as $className => $class) {
            foreach ($geodesicCoordinates as $coordinatesString => $expected) {
                $coordinates = explode(', ', $coordinatesString);

                yield sprintf('%s(%s, %s)', $className, $coordinates[0], $coordinates[1]) => [$class, $coordinates[0], $coordinates[1], $expected[0], $expected[1]];
            }
        }
    }

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>}, null, void>
     */
    public static function pointTypeProvider(): \Generator
    {
        yield 'GeometricPoint' => [GeometricPoint::class];

        yield 'GeographicPoint' => [GeographicPoint::class];
    }

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>, 1: string, 2: string, 3: string}, null, void>
     */
    public static function rangeExceptionProvider(): \Generator
    {
        $points = [
            'GeometricPoint' => GeometricPoint::class,
            'GeographicPoint' => GeographicPoint::class,
        ];

        $exceptions = [
            'Out of range latitude' => [
                '79:56:55W', '92:26:46N',
                'Out of range latitude value, latitude must be between -90 and 90, got "92:26:46N".',
            ],
            'Out of range longitude' => [
                '190:56:55W', '84:26:46N',
                'Out of range longitude value, longitude must be between -180 and 180, got "190:56:55W".',
            ],
            'Invalid latitude direction' => [
                '100:56:55W', '84:26:46Q',
                'Invalid coordinate value, got "84:26:46Q".',
            ],
            'Latitude minutes greater than 59' => [
                '108:42:55W', '84:64:46N',
                'Out of range minute value, minute must be between 0 and 59, got "84:64:46N".',
            ],
            'Latitude seconds greater than 59' => [
                '108:42:55W', '84:23:75N',
                'Out of range second value, second must be between 0 and 59, got "84:23:75N".',
            ],
            'Longitude degrees greater than 180' => [
                '190:56:55W', '84:26:46N',
                'Out of range longitude value, longitude must be between -180 and 180, got "190:56:55W".',
            ],
            'Invalid longitude direction' => [
                '100:56:55P', '84:26:46N',
                'Invalid coordinate value, got "100:56:55P".',
            ],
            'Longitude minutes greater than 59' => [
                '108:62:55W', '84:26:46N',
                'Out of range minute value, minute must be between 0 and 59, got "108:62:55W".',
            ],
            'Longitude seconds greater than 59' => [
                '108:53:94W', '84:26:46N',
                'Out of range second value, second must be between 0 and 59, got "108:53:94W".',
            ],
        ];

        foreach ($points as $className => $class) {
            foreach ($exceptions as $dataTestName => $dataTest) {
                yield sprintf('%s with a %s', $dataTestName, $className) => [$class, $dataTest[0], $dataTest[1], $dataTest[2]];
            }
        }
    }

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>, 1: string}, null, void>
     */
    public static function setterProvider(): \Generator
    {
        $points = [
            'GeometricPoint' => GeometricPoint::class,
            'GeographicPoint' => GeographicPoint::class,
        ];
        $methods = [
            'setX',
            'setY',
            'setLongitude',
            'setLatitude',
        ];
        foreach ($points as $className => $class) {
            foreach ($methods as $method) {
                yield sprintf('%s with %s', $className, $method) => [$class, $method];
            }
        }
    }

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>, 1: float|int|string, 2: float|int|string, 3: float|int, 4: float|int}, null, void>
     */
    public static function validGeodesicCoordinateProvider(): \Generator
    {
        $points = [
            'GeometricPoint' => GeometricPoint::class,
            'GeographicPoint' => GeographicPoint::class,
        ];
        foreach ($points as $point) {
            foreach (LoDataProvider::validGeodesicCoordinateProvider() as $key => $value) {
                yield sprintf('%s(%s)', $point, $key) => array_merge([$point], $value);
            }
        }
    }

    /**
     * Assert that the object has the method.
     *
     * @param object $object object to test
     * @param string $method the method to test
     */
    private static function assertObjectHasMethod(object $object, string $method): void
    {
        static::assertTrue(method_exists($object, $method), sprintf('Method "%s":"%s" does not exist.', $object::class, $method));
    }

    /**
     * Test getType method.
     *
     * @param class-string<AbstractPoint> $class the classname to test, Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testGetType(string $class): void
    {
        $point = new $class(0, 0);
        static::assertEquals('Point', $point->getType());
    }

    /**
     * Test geodesic setters.
     *
     * @param class-string<AbstractPoint> $pointType         Geometric or geographic point
     * @param float|int|string            $longitude         the actual longitude
     * @param float|int|string            $latitude          the actual latitude
     * @param float|int                   $expectedLongitude the expected longitude
     * @param float|int                   $expectedLatitude  the expected latitude
     *
     * @throws InvalidValueException It shall NOT happen in this test
     */
    #[DataProvider('validGeodesicCoordinateProvider')]
    public function testGoodGeodesicCoordinate(string $pointType, float|int|string $longitude, float|int|string $latitude, float|int $expectedLongitude, float|int $expectedLatitude): void
    {
        $geographicPoint = new $pointType(0, 0);
        $geographicPoint->setLongitude($longitude);
        $geographicPoint->setLatitude($latitude);

        static::assertSame($expectedLongitude, $geographicPoint->getLongitude());
        static::assertSame($expectedLatitude, $geographicPoint->getLatitude());
    }

    /**
     * Test valid string points.
     *
     * @param class-string<AbstractPoint> $abstractPoint     Geometric point and geographic point
     * @param float|int|string            $longitude         the longitude to test
     * @param float|int|string            $latitude          the latitude to test
     * @param float|int                   $expectedLongitude the expected longitude
     * @param float|int                   $expectedLatitude  the expected latitude
     */
    #[DataProvider('mixedGeodesicCoordinateProvider')]
    public function testGoodStringPoints(string $abstractPoint, float|int|string $longitude, float|int|string $latitude, float|int $expectedLongitude, float|int $expectedLatitude): void
    {
        $point = new $abstractPoint($longitude, $latitude);
        static::assertEqualsWithDelta($expectedLongitude, $point->getLongitude(), self::DELTA);
        static::assertEqualsWithDelta($expectedLatitude, $point->getLatitude(), self::DELTA);

        $point = new $abstractPoint(0, 0, 4326);
        $point->setLongitude($longitude);
        $point->setLatitude($latitude);
        static::assertEqualsWithDelta($expectedLongitude, $point->getLongitude(), self::DELTA);
        static::assertEqualsWithDelta($expectedLatitude, $point->getLatitude(), self::DELTA);
    }

    /**
     * Test to convert point to json.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testJson(string $abstractPoint): void
    {
        $expected = '{"type":"Point","coordinates":[5,5],"srid":null}';
        $point = new $abstractPoint(5, 5);

        static::assertEquals($expected, $point->toJson());
        static::assertEquals($expected, json_encode($point));

        $point->setSrid(4326);
        $expected = '{"type":"Point","coordinates":[5,5],"srid":4326}';
        static::assertEquals($expected, $point->toJson());
        static::assertEquals($expected, json_encode($point));
    }

    /**
     * Test point with srid.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testPointWithSrid(string $abstractPoint): void
    {
        $point = new $abstractPoint(5, 5, 2154);
        $actual = $point->getSrid();
        static::assertSame(2154, $actual);

        $point->setSrid(4326);
        $actual = $point->getSrid();
        static::assertSame(4326, $actual);
    }

    /**
     * This test checks that the geo-parser range exceptions are caught and "converted" to InvalidValueException.
     * This test focuses on constructor.
     *
     * @param class-string<AbstractPoint> $abstractPoint    Geometric point and geographic point
     * @param string                      $firstCoordinate  the first coordinate to test
     * @param string                      $secondCoordinate the second coordinate to test
     * @param string                      $expectedMessage  the expected message
     */
    #[DataProvider('rangeExceptionProvider')]
    public function testRangeExceptionAreCaughtWithConstructor(string $abstractPoint, string $firstCoordinate, string $secondCoordinate, string $expectedMessage): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage($expectedMessage);

        new $abstractPoint($firstCoordinate, $secondCoordinate);
    }

    /**
     * This test checks that the geo-parser range exceptions are caught and "converted" to InvalidValueException.
     * This test focuses on X and Y setters.
     *
     * @param class-string<AbstractPoint> $abstractPoint    Geometric point and geographic point
     * @param string                      $firstCoordinate  the first coordinate to test
     * @param string                      $secondCoordinate the second coordinate to test
     * @param string                      $expectedMessage  the expected message
     */
    #[DataProvider('rangeExceptionProvider')]
    public function testRangeExceptionAreCaughtWithNonExpectedSetters(string $abstractPoint, string $firstCoordinate, string $secondCoordinate, string $expectedMessage): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage($expectedMessage);

        $point = new $abstractPoint(0, 0);
        $point->setX($firstCoordinate);
        $point->setY($secondCoordinate);
    }

    /**
     * This test checks that the geo-parser range exceptions are caught and "converted" to InvalidValueException.
     * This test focuses on longitude and latitude setters.
     *
     * @param class-string<AbstractPoint> $abstractPoint    Geometric point and geographic point
     * @param string                      $firstCoordinate  the first coordinate to test
     * @param string                      $secondCoordinate the second coordinate to test
     * @param string                      $expectedMessage  the expected message
     */
    #[DataProvider('rangeExceptionProvider')]
    public function testRangeExceptionAreCaughtWithSetters(string $abstractPoint, string $firstCoordinate, string $secondCoordinate, string $expectedMessage): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage($expectedMessage);

        $point = new $abstractPoint(0, 0);
        $point->setLongitude($firstCoordinate);
        $point->setLatitude($secondCoordinate);
    }

    /**
     * Test setX method.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     * @param string                      $setter        the setter to test
     * @param array{0: string, 1: string} $getters       the getters to test
     * @param float|int|string            $actual        the actual value to set
     * @param float|int                   $expected      the expected value
     *
     * @throws InvalidValueException it should NOT happen
     */
    #[DataProvider('easyValuesProvider')]
    public function testSetters(string $abstractPoint, string $setter, array $getters, float|int|string $actual, float|int $expected): void
    {
        $firstGetter = $getters[0];
        $secondGetter = $getters[1];

        $point = new $abstractPoint(10, 10);
        self::assertObjectHasMethod($point, $setter);
        self::assertObjectHasMethod($point, $firstGetter);
        self::assertObjectHasMethod($point, $secondGetter);

        $point->{$setter}($actual);
        static::assertSame($expected, $point->{$firstGetter}());
        static::assertSame($expected, $point->{$secondGetter}());
    }

    /**
     * This test checks that an exception is thrown when passing two coordinates separated by a space.
     * This test checks all setters.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     * @param string                      $method        the method to test
     */
    #[DataProvider('setterProvider')]
    public function testSettersWithAnArray(string $abstractPoint, string $method): void
    {
        $point = new $abstractPoint(10, 10);

        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage('Invalid coordinate value, coordinate cannot be an array.');
        static::assertTrue(method_exists($point, $method), sprintf('Method "%s":"%s" does not exist.', $abstractPoint, $method));
        $point->{$method}('10 20');
    }

    /**
     * Test to convert point to array.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testToArray(string $abstractPoint): void
    {
        $expected = [-10, 11];
        $point = new $abstractPoint(-10, 11);
        $actual = $point->toArray();
        static::assertEquals($expected, $actual);

        $expected = [-42.42, 42.43];
        $point = new $abstractPoint(-42.42, 42.43);
        $actual = $point->toArray();
        static::assertEquals($expected, $actual);
    }

    /**
     * Test to convert point to string.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testToString(string $abstractPoint): void
    {
        $point = new $abstractPoint(34.0522, -18.2430);
        static::assertSame('34.0522 -18.243', $point->__toString());
        static::assertSame('34.0522 -18.243', (string) $point);

        $point = new $abstractPoint(34, -18);
        static::assertSame('34 -18', $point->__toString());
        static::assertSame('34 -18', (string) $point);
    }
}
