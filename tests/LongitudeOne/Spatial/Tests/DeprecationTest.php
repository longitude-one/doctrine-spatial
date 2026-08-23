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

namespace LongitudeOne\Spatial\Tests;

use Doctrine\Deprecations\PHPUnit\VerifyDeprecations;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geography\Point;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Tests constructor deprecations.
 *
 * @internal
 *
 * @covers \LongitudeOne\Spatial\PHP\Types\AbstractPoint
 * @covers \LongitudeOne\Spatial\PHP\Types\Geography\Point
 * @covers \LongitudeOne\Spatial\PHP\Types\Geometry\Point
 */
#[Group('php')]
class DeprecationTest extends TestCase
{
    use VerifyDeprecations;

    /**
     * Use an array with two coordinates as constructor is deprecated.
     *
     * @throws InvalidValueException it shall NOT happen in this test
     */
    public function testDeprecationWithAnArrayAsConstructor(): void
    {
        $this->expectDeprecationWithIdentifier('https://github.com/longitude-one/doctrine-spatial/issues/81');
        new Point([0, 0]);
    }

    /**
     * Use an array with two coordinates as constructor is deprecated.
     *
     * @throws InvalidValueException it shall NOT happen in this test
     */
    public function testDeprecationWithAnArrayAsConstructorAndSrid(): void
    {
        $this->expectDeprecationWithIdentifier('https://github.com/longitude-one/doctrine-spatial/issues/81');
        new Point([0, 0], 4326);
    }

    /**
     * No deprecations are expected when providing two coordinates as constructor and an optional Srid.
     * Even if coordinates are provided as float, integer, strings or as degrees, minutes and seconds.
     *
     * @throws InvalidValueException it shall NOT happen in this test
     */
    public function testNoDeprecations(): void
    {
        $this->expectNoDeprecationWithIdentifier('https://github.com/longitude-one/doctrine-spatial/issues/81');
        new Point(0, 0);
        new Point(0, 0, 4326);
        new Point(0.0, 0.0);
        new Point(0.0, 0.0, 4326);
        new Point('0.0', '0.0');
        new Point('0.0', '0.0', 4326);
        new Point('79°56′55″W', '40°26′46″N');
        new Point('99°56′55″W', '40°26′46″N', 4326);
    }
}
