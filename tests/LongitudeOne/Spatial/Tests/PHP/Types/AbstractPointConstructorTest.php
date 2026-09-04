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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Constructor input contract shared by geometric and geographic points.
 *
 * @internal
 */
#[CoversClass(AbstractPoint::class)]
#[CoversClass(GeographicPoint::class)]
#[CoversClass(GeometricPoint::class)]
#[Group('php')]
class AbstractPointConstructorTest extends AbstractPointTestCase
{
    // phpcs:disable Squiz.Commenting.FunctionComment.IncorrectTypeHint

    /**
     * Test bad array parameter - Object as value.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testArrayWithObject(string $abstractPoint): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Invalid parameters passed to %s::__construct: array(object, NULL)', $abstractPoint));

        new $abstractPoint([new \stdClass(), null]);
    }

    /**
     * SRID values passed to constructors must be integers or null.
     *
     * @param class-string<AbstractPoint> $pointType Geometric point and geographic point
     * @param mixed                       $srid      invalid SRID value
     */
    #[DataProvider('invalidSridProvider')]
    public function testConstructorRejectsNonIntegerSrid(string $pointType, mixed $srid): void
    {
        self::expectException(InvalidValueException::class);

        new $pointType('1', '2', $srid);
    }

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>, 1: mixed}, null, void>
     */
    public static function invalidSridProvider(): \Generator
    {
        $invalidSrids = [
            'numeric string' => '4326',
            'float' => 4326.9,
        ];

        foreach (self::pointTypeProvider() as $pointName => [$pointType]) {
            foreach ($invalidSrids as $sridName => $srid) {
                yield sprintf('%s with %s SRID', $pointName, $sridName) => [$pointType, $srid];
            }
        }
    }

    /**
     * Test argument 1 with too few values - Two invalid parameters.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testInvalidArrayWithTooFewValues(string $abstractPoint): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Invalid parameters passed to %s::__construct: array(1)', $abstractPoint));

        new $abstractPoint([1]);
    }

    /**
     * Test argument 1 with too many values - Two invalid parameters.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testInvalidArrayWithTooManyValues(string $abstractPoint): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Invalid parameters passed to %s::__construct: array(1, 2, 3, 4)', $abstractPoint));

        new $abstractPoint([1, 2, 3, 4]);
    }

    /**
     * Deprecated array-based constructors must reject associative arrays rather than passing named arguments.
     *
     * @param class-string<AbstractPoint> $pointType Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testLegacyArrayWithAssociativeKeys(string $pointType): void
    {
        self::expectException(InvalidValueException::class);

        new $pointType(['unexpected' => '1', 'other' => '2']);
    }

    /**
     * Deprecated array-based constructors must validate every coordinate before invoking construct().
     *
     * @param class-string<AbstractPoint> $pointType         Geometric point and geographic point
     * @param mixed                       $invalidCoordinate invalid coordinate value
     */
    #[DataProvider('invalidLegacyArrayCoordinateProvider')]
    public function testLegacyArrayWithInvalidCoordinate(string $pointType, mixed $invalidCoordinate): void
    {
        self::expectException(InvalidValueException::class);

        new $pointType([$invalidCoordinate, '2'], 4326);
    }

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>, 1: mixed}, null, void>
     */
    public static function invalidLegacyArrayCoordinateProvider(): \Generator
    {
        $invalidCoordinates = [
            'nested array' => ['1'],
            'object' => new \stdClass(),
            'closure' => static function (): void {
            },
            'boolean' => true,
        ];

        foreach (self::pointTypeProvider() as $pointName => [$pointType]) {
            foreach ($invalidCoordinates as $coordinateName => $invalidCoordinate) {
                yield sprintf('%s with %s', $pointName, $coordinateName) => [$pointType, $invalidCoordinate];
            }
        }
    }

    /**
     * Deprecated array-based constructors must reject resources before invoking construct().
     *
     * @param class-string<AbstractPoint> $pointType Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testLegacyArrayWithResource(string $pointType): void
    {
        $resource = fopen('php://memory', 'r');
        if (false === $resource) {
            static::fail('Unable to open the in-memory resource.');
        }

        try {
            self::expectException(InvalidValueException::class);

            new $pointType([$resource, '2'], 4326);
        } finally {
            fclose($resource);
        }
    }

    /**
     * Deprecated array-based constructors with a separate SRID must not reinterpret malformed arrays.
     *
     * @param class-string<AbstractPoint> $pointType   Geometric point and geographic point
     * @param mixed[]                     $coordinates malformed coordinate array
     */
    #[DataProvider('invalidLegacyArrayWithSridProvider')]
    public function testLegacyArrayWithSridHasExactlyTwoCoordinates(string $pointType, array $coordinates): void
    {
        self::expectException(InvalidValueException::class);

        new $pointType($coordinates, 4326);
    }

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>, 1: mixed[]}, null, void>
     */
    public static function invalidLegacyArrayWithSridProvider(): \Generator
    {
        $invalidCoordinates = [
            'no coordinate' => [],
            'one coordinate' => ['1'],
            'three coordinates' => ['1', '2', 3857],
            'four coordinates' => ['1', '2', 3857, 9999],
        ];

        foreach (self::pointTypeProvider() as $pointName => [$pointType]) {
            foreach ($invalidCoordinates as $coordinatesName => $coordinates) {
                yield sprintf('%s with %s', $pointName, $coordinatesName) => [$pointType, $coordinates];
            }
        }
    }

    /**
     * Test bad string parameters - No parameters.
     *
     * @param class-string<AbstractPoint> $pointType Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testMissingArguments(string $pointType): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Invalid parameters passed to %s::__construct:', $pointType));

        new $pointType();
    }

    /**
     * Test error when point is created with too many arguments.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testPointTooManyArguments(string $abstractPoint): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Invalid parameters passed to %s::__construct: 5, 5, 5, 5', $abstractPoint));

        new $abstractPoint(5, 5, 5, 5);
    }

    /**
     * Test error when point was created with the wrong arguments type.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testPointWrongArgumentTypes(string $abstractPoint): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Invalid parameters passed to %s::__construct: array, array, 1234', $abstractPoint));

        new $abstractPoint([], [], '1234');
    }

    /**
     * Test bad string parameters - Two invalid parameters.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testTwoInvalidArguments(string $abstractPoint): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Invalid parameters passed to %s::__construct: NULL, NULL', $abstractPoint));

        new $abstractPoint(null, null);
    }

    /**
     * Test bad string parameters - More than 3 parameters.
     *
     * @param class-string<AbstractPoint> $abstractPoint Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testUnusedArguments(string $abstractPoint): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Invalid parameters passed to %s::__construct: 1, 2, 3, 4, NULL, 5', $abstractPoint));

        new $abstractPoint(1, 2, 3, 4, null, 5);
    }
}
