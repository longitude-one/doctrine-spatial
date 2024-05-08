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
use LongitudeOne\Spatial\PHP\Types\Geometry\MultiPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use PHPUnit\Framework\TestCase;

/**
 * MultiPoint object tests.
 *
 * @group php
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 *
 * @coversDefaultClass
 */
class MultiPointTest extends TestCase
{
    /**
     * Test MultiPoint bad parameter.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testBadLineString(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid MultiPoint Point value of type "integer"');

        new MultiPoint([1, 2, 3, 4]);
    }

    /**
     * Test an empty multipoint.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testEmptyMultiPoint(): void
    {
        $multiPoint = new MultiPoint([]);

        static::assertEmpty($multiPoint->getPoints());
    }

    /**
     * Test to convert multipoint to json.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testJson(): void
    {
        $expected = '{"type":"MultiPoint","coordinates":[[0,0],[0,5],[5,0],[0,0]],"srid":null}';
        $multiPoint = new MultiPoint(
            [
                [0, 0],
                [0, 5],
                [5, 0],
                [0, 0],
            ]
        );

        static::assertEquals($expected, $multiPoint->toJson());
        static::assertEquals($expected, json_encode($multiPoint));

        $expected = '{"type":"MultiPoint","coordinates":[[0,0],[0,5],[5,0],[0,0]],"srid":4326}';
        $multiPoint->setSrid(4326);
        static::assertEquals($expected, $multiPoint->toJson());
        static::assertEquals($expected, json_encode($multiPoint));
    }

    /**
     * Test to add point to a multipoint.
     *
     * @throws InvalidValueException this should not happen
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiPointAddPoints(): void
    {
        $expected = [
            new Point(0, 0),
            new Point(1, 1),
            new Point(2, 2),
            new Point(3, 3),
        ];
        $multiPoint = new MultiPoint(
            [
                [0, 0],
                [1, 1],
            ]
        );

        $multiPoint
            ->addPoint([2, 2])
            ->addPoint([3, 3])
        ;

        $actual = $multiPoint->getPoints();

        static::assertCount(4, $actual);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test to get last point from a multipoint.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiPointFromArraysGetLastPoint(): void
    {
        $expected = new Point(3, 3);
        $multiPoint = new MultiPoint(
            [
                [0, 0],
                [1, 1],
                [2, 2],
                [3, 3],
            ]
        );
        $actual = $multiPoint->getPoint(-1);

        static::assertEquals($expected, $actual);
    }

    /**
     * Test to get points from a multipoint.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiPointFromArraysGetPoints(): void
    {
        $expected = [
            new Point(0, 0),
            new Point(1, 1),
            new Point(2, 2),
            new Point(3, 3),
        ];
        $multiPoint = new MultiPoint(
            [
                [0, 0],
                [1, 1],
                [2, 2],
                [3, 3],
            ]
        );
        $actual = $multiPoint->getPoints();

        static::assertCount(4, $actual);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test to get first point from a multipoint.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiPointFromArraysGetSinglePoint(): void
    {
        $expected = new Point(1, 1);
        $multiPoint = new MultiPoint(
            [
                [0, 0],
                [1, 1],
                [2, 2],
                [3, 3],
            ]
        );
        $actual = $multiPoint->getPoint(1);

        static::assertEquals($expected, $actual);
    }

    /**
     * Test to convert multipoint to string.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiPointFromArraysToString(): void
    {
        $expected = '0 0,0 5,5 0,0 0';
        $multiPoint = new MultiPoint(
            [
                [0, 0],
                [0, 5],
                [5, 0],
                [0, 0],
            ]
        );

        static::assertEquals($expected, (string) $multiPoint);
    }

    /**
     * Test to convert multipoint to array.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiPointFromObjectsToArray(): void
    {
        $expected = [
            [0, 0],
            [1, 1],
            [2, 2],
            [3, 3],
        ];
        $multiPoint = new MultiPoint([
            new Point(0, 0),
            new Point(1, 1),
            new Point(2, 2),
            new Point(3, 3),
        ]);

        static::assertCount(4, $multiPoint->getPoints());
        static::assertEquals($expected, $multiPoint->toArray());
    }
}
