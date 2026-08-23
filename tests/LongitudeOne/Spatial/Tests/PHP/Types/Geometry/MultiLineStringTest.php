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

namespace LongitudeOne\Spatial\Tests\PHP\Types\Geometry;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\MultiLineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * MultiLineString object tests.
 *
 * @internal
 *
 * @coversDefaultClass
 */
#[Group('php')]
class MultiLineStringTest extends TestCase
{
    /**
     * Test an empty multiline string.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testEmptyMultiLineString(): void
    {
        $multiLineString = new MultiLineString([]);

        static::assertEmpty($multiLineString->getLineStrings());
    }

    /**
     * Test to convert multiline string to json.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testJson(): void
    {
        $expected = '{"type":"MultiLineString","coordinates":[[[0,0],[10,0],[10,10],[0,10],[0,0]],[[0,0],[10,0],[10,10],[0,10],[0,0]]],"srid":null}';
        $lineStrings = [
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
        ];
        $multiLineString = new MultiLineString($lineStrings);

        static::assertEquals($expected, $multiLineString->toJson());
        static::assertEquals($expected, json_encode($multiLineString));
        $expected = '{"type":"MultiLineString","coordinates":[[[0,0],[10,0],[10,10],[0,10],[0,0]],[[0,0],[10,0],[10,10],[0,10],[0,0]]],"srid":4326}';
        $multiLineString->setSrid(4326);
        static::assertEquals($expected, $multiLineString->toJson());
        static::assertEquals($expected, json_encode($multiLineString));
    }

    /**
     * Test to convert a multiline string to a string.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiLineStringFromArraysToString(): void
    {
        $expected = '(0 0,10 0,10 10,0 10,0 0),(0 0,10 0,10 10,0 10,0 0)';
        $lineStrings = [
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
        ];
        $multiLineString = new MultiLineString($lineStrings);
        $result = (string) $multiLineString;

        static::assertEquals($expected, $result);
    }

    /**
     * Test to get last line from multiline string.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiLineStringFromObjectsGetLastLineString(): void
    {
        $firstLineString = new LineString(
            [
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]
        );
        $lastLineString = new LineString(
            [
                new Point(5, 5),
                new Point(7, 5),
                new Point(7, 7),
                new Point(5, 7),
                new Point(5, 5),
            ]
        );
        $polygon = new MultiLineString([$firstLineString, $lastLineString]);

        static::assertEquals($lastLineString, $polygon->getLineString(-1));
    }

    /**
     * Test to get first line from multiline string.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiLineStringFromObjectsGetSingleLineString(): void
    {
        $firstLineString = new LineString(
            [
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]
        );
        $lastLineString = new LineString(
            [
                new Point(5, 5),
                new Point(7, 5),
                new Point(7, 7),
                new Point(5, 7),
                new Point(5, 5),
            ]
        );
        $multiLineString = new MultiLineString([$firstLineString, $lastLineString]);

        static::assertEquals($firstLineString, $multiLineString->getLineString(0));
    }

    /**
     * Test to create multiline string from line string.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiLineStringFromObjectsToArray(): void
    {
        $expected = [
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
        ];
        $lineStrings = [
            new LineString(
                [
                    new Point(0, 0),
                    new Point(10, 0),
                    new Point(10, 10),
                    new Point(0, 10),
                    new Point(0, 0),
                ]
            ),
            new LineString(
                [
                    new Point(0, 0),
                    new Point(10, 0),
                    new Point(10, 10),
                    new Point(0, 10),
                    new Point(0, 0),
                ]
            ),
        ];

        $multiLineString = new MultiLineString($lineStrings);

        static::assertEquals($expected, $multiLineString->toArray());
    }

    /**
     * Test a solid multiline string.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testSolidMultiLineStringAddRings(): void
    {
        $expected = [
            new LineString(
                [
                    new Point(0, 0),
                    new Point(10, 0),
                    new Point(10, 10),
                    new Point(0, 10),
                    new Point(0, 0),
                ]
            ),
            new LineString(
                [
                    new Point(0, 0),
                    new Point(10, 0),
                    new Point(10, 10),
                    new Point(0, 10),
                    new Point(0, 0),
                ]
            ),
        ];
        $rings = [
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
        ];

        $multiLineString = new MultiLineString($rings);

        $multiLineString->addLineString(
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ]
        );

        static::assertEquals($expected, $multiLineString->getLineStrings());
    }

    /**
     * Test a solid multiline string.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testSolidMultiLineStringFromArraysGetRings(): void
    {
        $expected = [
            new LineString(
                [
                    new Point(0, 0),
                    new Point(10, 0),
                    new Point(10, 10),
                    new Point(0, 10),
                    new Point(0, 0),
                ]
            ),
            new LineString(
                [
                    new Point(0, 0),
                    new Point(10, 0),
                    new Point(10, 10),
                    new Point(0, 10),
                    new Point(0, 0),
                ]
            ),
        ];
        $rings = [
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
        ];

        $multiLineString = new MultiLineString($rings);

        static::assertEquals($expected, $multiLineString->getLineStrings());
    }
}
