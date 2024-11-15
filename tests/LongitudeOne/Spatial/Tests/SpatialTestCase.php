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

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use PHPUnit\Framework\TestCase;

/**
 * Spatial test case.
 *
 * This class provides assertions to test spatial types.
 *
 * @internal
 *
 * @coversNothing
 */
class SpatialTestCase extends TestCase
{
    /**
     * Assert big polygon.
     *
     * @param mixed            $value    Value to test
     * @param AbstractPlatform $platform the platform
     */
    protected static function assertBigPolygon($value, AbstractPlatform $platform): void
    {
        $expected = 'POLYGON((0 10,10 10,10 0,0 0,0 10))';
        if ($platform instanceof MySQLPlatform) {
            // MySQL does not respect creation order of points composing a Polygon.
            $expected = 'POLYGON((0 10,0 0,10 0,10 10,0 10))';
        }

        static::assertSame($expected, $value);
    }

    /**
     * Assert empty geometry.
     * MySQL5 does not return the standard answer, but this bug was solved in MySQL8.
     * So test for an empty geometry is a little more complex than to compare two strings.
     *
     * @param mixed                 $value    Value to test
     * @param null|AbstractPlatform $platform the platform
     */
    protected static function assertEmptyPoint($value, ?AbstractPlatform $platform = null): void
    {
        $expected = 'POINT EMPTY';

        if (self::platformIsMySql57($platform)) {
            // MySQL5 does not return the standard answer
            $expected = 'GEOMETRYCOLLECTION()';
        }

        if ($platform instanceof MySQL80Platform) {
            // MySQL8 does not return the standard answer
            $expected = 'GEOMETRYCOLLECTION EMPTY';
        }

        static::assertSame($expected, $value);
    }

    /**
     * Check if the platform is MySQL 5.7.
     *
     * With doctrine/orm:3.0 MySQL57Platform does not exist anymore.
     *
     * @param null|AbstractPlatform $platform the platform
     */
    protected static function platformIsMySql57(?AbstractPlatform $platform): bool
    {
        return null !== $platform
            && 'Doctrine\DBAL\Platforms\MySQL57Platform' === $platform::class
            || $platform instanceof MySQLPlatform
            && 'Doctrine\DBAL\Platforms\MySQL80Platform' !== $platform::class;
    }
}
