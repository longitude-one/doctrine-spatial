<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
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

namespace LongitudeOne\Spatial\Tests;

class DataProvider
{
    /**
     * @return \Generator<string, array{0: float|int|string}, null, void>
     */
    public static function outOfRangeLatitudeProvider(): \Generator
    {
        yield 'int(-100)' => [-100];

        yield 'float(-90.01)' => [-90.01];

        yield 'string(-100)' => ['-100'];

        yield 'string(-100°)' => ['-100°'];

        yield 'int(100)' => [100];

        yield 'float(90.01)' => [90.01];

        yield 'string(100°)' => ['100°'];
    }

    /**
     * @return \Generator<string, array{0: float|int|string}, null, void>
     */
    public static function outOfRangeLongitudeProvider(): \Generator
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
     * @return \Generator<string, array{0: float|int|string, 1: float|int|string, 2: float|int, 3: float|int}, null, void>
     */
    public static function validGeodesicCoordinateProvider(): \Generator
    {
        // Integers
        yield 'int(42), int(42)' => [42, 42, 42, 42];

        yield 'int(-42), int(-42)' => [-42, -42, -42, -42];

        yield 'int(180), int(90)' => [180, 90, 180, 90];

        yield 'int(-180), int(-90)' => [-180, -90, -180, -90];

        // Floats
        yield 'float(42.42), float(42.42)' => [42.42, 42.42, 42.42, 42.42];

        yield 'float(-42.42), float(-42.42)' => [-42.42, -42.42, -42.42, -42.42];

        yield 'float(180.0), float(90.0)' => [180.0, 90.0, 180.0, 90.0];

        yield 'float(-180.0), float(-90.0)' => [-180.0, -90.0, -180.0, -90.0];

        // Strings
        yield 'string(42), string(42)' => ['42', '42', 42, 42];

        yield 'string(-42), string(-42)' => ['-42', '-42', -42, -42];

        yield 'string(180), string(90)' => ['180', '90', 180, 90];

        yield 'string(-180), string(-90)' => ['-180', '-90', -180, -90];

        // Strings with degrees
        yield 'string(42°), string(42°)' => ['42°', '42°', 42.0, 42.0];

        yield 'string(-42°), string(-42°)' => ['-42°', '-42°', -42.0, -42.0];

        yield 'string(180°), string(90°)' => ['180°', '90°', 180.0, 90.0];

        yield 'string(-180°), string(-90°)' => ['-180°', '-90°', -180.0, -90.0];

        // Strings with degrees and minutes
        yield "string(42°42'), string(42°42')" => ["42°42'", "42°42'", 42.7, 42.7];

        yield "string(-42°42'), string(-42°42')" => ["-42°42'", "-42°42'", -42.7, -42.7];

        yield "string(180°0'), string(90°0')" => ["180°0'", "90°0'", 180.0, 90.0];

        yield "string(-180°0'), string(-90°0')" => ["-180°0'", "-90°0'", -180.0, -90.0];
    }
}
