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

namespace LongitudeOne\Spatial\Tests\Helper;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geography\Point as GeographyPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point as GeometryPoint;

/**
 * PointHelperTrait Trait.
 *
 * This helper provides some methods to generate point entities.
 *
 * TODO All of these points will be defined in test documentation.
 *
 * Point Origin (0 0)
 * Point A (1 1)
 * Point B (2 2)
 * Point C (3 3)
 * Point D (4 4)
 * Point E (5 5)
 *
 * Methods beginning with create will create a geo* entity in database, but won't store it in database.
 * Methods beginning with persist will store a geo* entity in database.
 *
 * @see /docs/Test.rst
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 */
trait PointHelperTrait
{
    /**
     * Create Los Angeles geography Point entity.
     */
    protected static function createLosAngelesGeography(): GeographyPoint
    {
        return self::createGeographyPoint('Los Angeles', '-118.2430', '34.0522');
    }

    /**
     * Create Los Angeles geometry Point entity.
     */
    protected static function createLosAngelesGeometry(): GeometryPoint
    {
        return self::createGeometryPoint('Los Angeles', '-118.2430', '34.0522');
    }

    /**
     * Create Point A (1 1).
     */
    protected static function createPointA(): GeometryPoint
    {
        return self::createGeometryPoint('a', '1', '1');
    }

    /**
     * Create Point B (2 2).
     */
    protected static function createPointB(): GeometryPoint
    {
        return self::createGeometryPoint('B', '2', '2');
    }

    /**
     * Create Point C (3 3).
     */
    protected static function createPointC(): GeometryPoint
    {
        return self::createGeometryPoint('C', '3', '3');
    }

    /**
     * Create Point D (4 4).
     */
    protected static function createPointD(): GeometryPoint
    {
        return self::createGeometryPoint('D', '4', '4');
    }

    /**
     * Create Point E (5 5).
     */
    protected static function createPointE(): GeometryPoint
    {
        return self::createGeometryPoint('E', '5', '5');
    }

    /**
     * Create Point Origin O (0 0).
     */
    protected static function createPointOrigin(): GeometryPoint
    {
        return self::createGeometryPoint('O', '0', '0');
    }

    /**
     * Create Point E (5 5) with SRID.
     *
     * @param int $srid SRID of geometry point E
     */
    protected static function createPointWithSrid(int $srid): GeometryPoint
    {
        try {
            return new GeometryPoint('5', '5', $srid);
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to create point E (5 5) with srid %d: %s', $srid, $e->getMessage()));
        }
    }

    /**
     * Create a geography point.
     *
     * @param string $name name is only used when an exception is thrown
     * @param string $x    X coordinate
     * @param string $y    Y coordinate
     */
    private static function createGeographyPoint(string $name, string $x, string $y): GeographyPoint
    {
        try {
            return new GeographyPoint($x, $y);
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to create point %s(%d %d): %s', $name, $x, $y, $e->getMessage()));
        }
    }

    /**
     * Create a geometry point.
     *
     * @param string $name name is only used when an exception is thrown
     * @param string $x    X coordinate
     * @param string $y    Y coordinate
     */
    private static function createGeometryPoint(string $name, string $x, string $y): GeometryPoint
    {
        try {
            return new GeometryPoint($x, $y);
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to create point %s(%d %d): %s', $name, $x, $y, $e->getMessage()));
        }
    }

    /**
     * Create New York geography point.
     */
    private static function createNewYorkGeography(): GeographyPoint
    {
        return self::createGeographyPoint('New-York', '-73.938611', '40.664167');
    }

    /**
     * Create New York geometry point.
     */
    private static function createNewYorkGeometry(): GeometryPoint
    {
        return self::createGeometryPoint('New-York', '-73.938611', '40.664167');
    }
}
