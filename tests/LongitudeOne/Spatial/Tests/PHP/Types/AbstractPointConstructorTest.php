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
     * Invalid scalar types are rejected by the constructor signature in strict mode.
     *
     * @param class-string<AbstractPoint> $pointType Geometric point and geographic point
     * @param mixed[]                     $arguments constructor arguments
     */
    #[DataProvider('invalidArgumentsProvider')]
    public function testConstructorRejectsInvalidTypes(string $pointType, array $arguments): void
    {
        self::expectException(\TypeError::class);

        new $pointType(...$arguments);
    }

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>, 1: mixed[]}, null, void>
     */
    public static function invalidArgumentsProvider(): \Generator
    {
        $invalidArguments = [
            'null X' => [null, 2],
            'null Y' => [1, null],
            'boolean X' => [true, 2],
            'boolean Y' => [1, false],
            'object X' => [new \stdClass(), 2],
            'object Y' => [1, new \stdClass()],
            'array Y' => [1, [2]],
            'string SRID' => [1, 2, '4326'],
            'float SRID' => [1, 2, 4326.9],
        ];

        foreach (self::pointTypeProvider() as $pointName => [$pointType]) {
            foreach ($invalidArguments as $name => $arguments) {
                yield sprintf('%s with %s', $pointName, $name) => [$pointType, $arguments];
            }
        }
    }

    /**
     * Constructors reject every legacy array form, including previously valid inputs.
     *
     * @param class-string<AbstractPoint> $pointType Geometric point and geographic point
     * @param mixed[]                     $arguments constructor arguments
     */
    #[DataProvider('legacyArgumentsProvider')]
    public function testConstructorRejectsLegacyArrays(string $pointType, array $arguments): void
    {
        self::expectException(\TypeError::class);

        new $pointType(...$arguments);
    }

    /**
     * @return \Generator<string, array{0: class-string<AbstractPoint>, 1: mixed[]}, null, void>
     */
    public static function legacyArgumentsProvider(): \Generator
    {
        $legacyArguments = [
            'coordinate array' => [[1, 2]],
            'array containing SRID' => [[1, 2, 4326]],
            'array with separate SRID' => [[1, 2], 4326],
            'associative array' => [['x' => 1, 'y' => 2]],
            'empty array' => [[]],
        ];

        foreach (self::pointTypeProvider() as $pointName => [$pointType]) {
            foreach ($legacyArguments as $name => $arguments) {
                yield sprintf('%s with %s', $pointName, $name) => [$pointType, $arguments];
            }
        }
    }

    /**
     * Both coordinates are required.
     *
     * @param class-string<AbstractPoint> $pointType Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testMissingArguments(string $pointType): void
    {
        self::expectException(\ArgumentCountError::class);

        new $pointType();
    }

    /**
     * A single scalar coordinate is insufficient.
     *
     * @param class-string<AbstractPoint> $pointType Geometric point and geographic point
     */
    #[DataProvider('pointTypeProvider')]
    public function testMissingSecondCoordinate(string $pointType): void
    {
        self::expectException(\ArgumentCountError::class);

        new $pointType(1);
    }
}
