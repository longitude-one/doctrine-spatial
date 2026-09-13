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

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\AbstractPoint;
use LongitudeOne\Spatial\PHP\Types\Geography\Point as GeographicPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point as GeometricPoint;
use LongitudeOne\Spatial\Tests\DataProvider as LoDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Coordinate parsing, validation, mutation and aliases shared by point types.
 *
 * @internal
 */
#[CoversClass(AbstractPoint::class)]
#[CoversClass(GeographicPoint::class)]
#[CoversClass(GeometricPoint::class)]
#[Group('php')]
class AbstractPointCoordinateTest extends AbstractPointTestCase
{
    // phpcs:disable Squiz.Commenting.FunctionComment.IncorrectTypeHint

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
        $point = new $pointType(0, 0);
        $point->setLongitude($longitude);
        $point->setLatitude($latitude);

        static::assertSame($expectedLongitude, $point->getLongitude());
        static::assertSame($expectedLatitude, $point->getLatitude());
    }

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>, 1: float|int|string, 2: float|int|string, 3: float|int, 4: float|int}, null, void>
     */
    public static function validGeodesicCoordinateProvider(): \Generator
    {
        foreach (self::pointTypeProvider() as $pointName => [$pointType]) {
            foreach (LoDataProvider::validGeodesicCoordinateProvider() as $key => $value) {
                yield sprintf('%s(%s)', $pointName, $key) => array_merge([$pointType], $value);
            }
        }
    }

    /**
     * Test valid string points through construction and mutation.
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
     * @return \Generator<string, array{0: class-string<AbstractPoint>, 1: float|int|string, 2: float|int|string, 3: float|int, 4: float|int}, null, void>
     */
    public static function mixedGeodesicCoordinateProvider(): \Generator
    {
        $geodesicCoordinates = [
            '79:56:55W, 40:26:46N' => [-79.9486111111111, 40.44611111111111],
            '79° 56\' 55" W, 40° 26\' 46" N' => [-79.9486111111111, 40.44611111111111],
            '79°56′55″W, 40°26′46″N' => [-79.9486111111111, 40.44611111111111],
            '79° 56′ 55″ W, 40° 26′ 46″ N' => [-79.9486111111111, 40.44611111111111],
            '79:56:55.832W, 40:26:46.543N' => [-79.94884222222223, 40.446261944444444],
            '112:4:0W, 33:27:0N' => [-112.066666666666, 33.45],
        ];

        foreach (self::pointTypeProvider() as $className => [$class]) {
            foreach ($geodesicCoordinates as $coordinatesString => $expected) {
                $coordinates = explode(', ', $coordinatesString);

                yield sprintf('%s(%s, %s)', $className, $coordinates[0], $coordinates[1]) => [$class, $coordinates[0], $coordinates[1], $expected[0], $expected[1]];
            }
        }
    }

    /**
     * Coordinate values in exceptions must be bounded and free of control characters.
     *
     * @param class-string<AbstractPoint> $pointType  Geometric point and geographic point
     * @param string                      $coordinate invalid coordinate value
     */
    #[DataProvider('unsafeCoordinateProvider')]
    public function testInvalidCoordinateExceptionDoesNotExposeUnsafeInput(string $pointType, string $coordinate): void
    {
        try {
            new $pointType($coordinate, '2');
            static::fail('An invalid coordinate must throw an exception.');
        } catch (InvalidValueException $exception) {
            static::assertLessThan(1024, mb_strlen($exception->getMessage()));
            static::assertStringNotContainsString("\r", $exception->getMessage());
            static::assertStringNotContainsString("\n", $exception->getMessage());
        }
    }

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>, 1: string}, null, void>
     */
    public static function unsafeCoordinateProvider(): \Generator
    {
        $coordinates = [
            'long value' => str_repeat('invalid-coordinate-', 1000),
            'control characters' => "invalid\r\ncoordinate",
        ];

        foreach (self::pointTypeProvider() as $pointName => [$pointType]) {
            foreach ($coordinates as $coordinateName => $coordinate) {
                yield sprintf('%s with %s', $pointName, $coordinateName) => [$pointType, $coordinate];
            }
        }
    }

    /**
     * This test checks that parser exceptions are converted by cartesian setters.
     *
     * @param class-string<AbstractPoint> $abstractPoint    Geometric point and geographic point
     * @param string                      $firstCoordinate  the first coordinate to test
     * @param string                      $secondCoordinate the second coordinate to test
     * @param string                      $expectedMessage  the expected message
     */
    #[DataProvider('rangeExceptionProvider')]
    public function testRangeExceptionAreCaughtWithCartesianSetters(string $abstractPoint, string $firstCoordinate, string $secondCoordinate, string $expectedMessage): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage($expectedMessage);

        $point = new $abstractPoint(0, 0);
        $point->setX($firstCoordinate);
        $point->setY($secondCoordinate);
    }

    /**
     * This test checks that the geo-parser range exceptions are caught and converted to InvalidValueException during construction.
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
     * This test checks that parser exceptions are converted by geodesic setters.
     *
     * @param class-string<AbstractPoint> $abstractPoint    Geometric point and geographic point
     * @param string                      $firstCoordinate  the first coordinate to test
     * @param string                      $secondCoordinate the second coordinate to test
     * @param string                      $expectedMessage  the expected message
     */
    #[DataProvider('rangeExceptionProvider')]
    public function testRangeExceptionAreCaughtWithGeodesicSetters(string $abstractPoint, string $firstCoordinate, string $secondCoordinate, string $expectedMessage): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage($expectedMessage);

        $point = new $abstractPoint(0, 0);
        $point->setLongitude($firstCoordinate);
        $point->setLatitude($secondCoordinate);
    }

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>, 1: string, 2: string, 3: string}, null, void>
     */
    public static function rangeExceptionProvider(): \Generator
    {
        $exceptions = [
            'Out of range latitude' => ['79:56:55W', '92:26:46N', 'Out of range latitude value, latitude must be between -90 and 90, got "92:26:46N".'],
            'Out of range longitude' => ['190:56:55W', '84:26:46N', 'Out of range longitude value, longitude must be between -180 and 180, got "190:56:55W".'],
            'Invalid latitude direction' => ['100:56:55W', '84:26:46Q', 'Invalid coordinate value, got "84:26:46Q".'],
            'Latitude minutes greater than 59' => ['108:42:55W', '84:64:46N', 'Out of range minute value, minute must be between 0 and 59, got "84:64:46N".'],
            'Latitude seconds greater than 59' => ['108:42:55W', '84:23:75N', 'Out of range second value, second must be between 0 and 59, got "84:23:75N".'],
            'Longitude degrees greater than 180' => ['190:56:55W', '84:26:46N', 'Out of range longitude value, longitude must be between -180 and 180, got "190:56:55W".'],
            'Invalid longitude direction' => ['100:56:55P', '84:26:46N', 'Invalid coordinate value, got "100:56:55P".'],
            'Longitude minutes greater than 59' => ['108:62:55W', '84:26:46N', 'Out of range minute value, minute must be between 0 and 59, got "108:62:55W".'],
            'Longitude seconds greater than 59' => ['108:53:94W', '84:26:46N', 'Out of range second value, second must be between 0 and 59, got "108:53:94W".'],
        ];

        foreach (self::pointTypeProvider() as $className => [$class]) {
            foreach ($exceptions as $dataTestName => $dataTest) {
                yield sprintf('%s with a %s', $dataTestName, $className) => [$class, $dataTest[0], $dataTest[1], $dataTest[2]];
            }
        }
    }

    /**
     * Test coordinate setters and their cartesian/geodesic aliases.
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
        $point = new $abstractPoint(10, 10);
        self::assertObjectHasMethod($point, $setter);
        self::assertObjectHasMethod($point, $getters[0]);
        self::assertObjectHasMethod($point, $getters[1]);

        $point->{$setter}($actual);
        static::assertSame($expected, $point->{$getters[0]}());
        static::assertSame($expected, $point->{$getters[1]}());
    }

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>, 1: string, 2: array{0: string, 1: string}, 3: float|int|string, 4: float|int}, null, void>
     */
    public static function easyValuesProvider(): \Generator
    {
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
            'string(20.0)' => ['actual' => '20.0', 'expected' => 20.0],
        ];

        foreach (self::pointTypeProvider() as $className => [$class]) {
            foreach ($methods as $setter => $getters) {
                foreach ($values as $valueName => $value) {
                    yield sprintf('%s with %s and %s', $className, $setter, $valueName) => [$class, $setter, $getters, $value['actual'], $value['expected']];
                }
            }
        }
    }

    /**
     * All setters must reject a string parsed as a coordinate pair.
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
        static::assertObjectHasMethod($point, $method);
        $point->{$method}('10 20');
    }

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>, 1: string}, null, void>
     */
    public static function setterProvider(): \Generator
    {
        foreach (self::pointTypeProvider() as $className => [$class]) {
            foreach (['setX', 'setY', 'setLongitude', 'setLatitude'] as $method) {
                yield sprintf('%s with %s', $className, $method) => [$class, $method];
            }
        }
    }
}
