<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 7.4 | 8.0
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017 - 2021
 * (c) Longitude One 2020 - 2021
 * (c) 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace LongitudeOne\Spatial\Tests\PHP\Types\Geometry;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\PHP\Types\Geometry\Polygon;
use LongitudeOne\Spatial\Tests\Helper\PolygonHelperTrait;
use PHPUnit\Framework\TestCase;

/**
 * Polygon object tests.
 *
 * @group php
 *
 * @internal
 * @coversDefaultClass
 */
class PolygonTest extends TestCase
{
    use PolygonHelperTrait;

    /**
     * Test an empty polygon.
     */
    public function testEmptyPolygon(): void
    {
        $polygon = $this->createEmptyPolygon();

        static::assertEmpty($polygon->getRings());
    }

    /**
     * Test to export json.
     */
    public function testJson(): void
    {
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $expected = '{"type":"Polygon","coordinates":[[[0,0],[10,0],[10,10],[0,10],[0,0]],[[0,0],[10,0],[10,10],[0,10],[0,0]]],"srid":null}';
        // phpcs:enable
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
        $polygon = new Polygon($rings);
        static::assertEquals($expected, $polygon->toJson());
        static::assertEquals($expected, json_encode($polygon));

        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $expected = '{"type":"Polygon","coordinates":[[[0,0],[10,0],[10,10],[0,10],[0,0]],[[0,0],[10,0],[10,10],[0,10],[0,0]]],"srid":4326}';
        // phpcs:enable
        $polygon->setSrid(4326);
        static::assertEquals($expected, $polygon->toJson());
        static::assertEquals($expected, json_encode($polygon));
    }

    /**
     * Test Polygon with open ring.
     */
    public function testOpenPolygonRing()
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid polygon, ring "(0 0,10 0,10 10,0 10)" is not closed');

        $rings = [
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
            ]),
        ];

        new Polygon($rings);
    }

    /**
     * Test to get last ring.
     */
    public function testRingPolygonFromObjectsGetLastRing()
    {
        $ringA = new LineString(
            [
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]
        );
        $ringB = new LineString(
            [
                new Point(5, 5),
                new Point(7, 5),
                new Point(7, 7),
                new Point(5, 7),
                new Point(5, 5),
            ]
        );
        $polygon = new Polygon([$ringA, $ringB]);

        static::assertEquals($ringB, $polygon->getRing(-1));
    }

    /**
     * Test to get the first ring.
     */
    public function testRingPolygonFromObjectsGetSingleRing()
    {
        $ringA = new LineString(
            [
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]
        );
        $ringB = new LineString(
            [
                new Point(5, 5),
                new Point(7, 5),
                new Point(7, 7),
                new Point(5, 7),
                new Point(5, 5),
            ]
        );
        $polygon = new Polygon([$ringA, $ringB]);

        static::assertEquals($ringA, $polygon->getRing(0));
    }

    /**
     * Test a solid polygon from array add rings.
     *
     * @throws InvalidValueException This should not happen
     */
    public function testSolidPolygonFromArrayAddRings()
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
                    new Point(2, 2),
                    new Point(10, 0),
                    new Point(10, 10),
                    new Point(0, 10),
                    new Point(2, 2),
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

        $polygon = new Polygon($rings);

        $polygon->addRing(
            [
                [2, 2],
                [10, 0],
                [10, 10],
                [0, 10],
                [2, 2],
            ]
        );

        static::assertEquals($expected, $polygon->getRings());
    }

    /**
     * Test a solid polygon from an array of points.
     */
    public function testSolidPolygonFromArrayOfPoints()
    {
        $expected = [
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
        ];
        $rings = [
            [
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ],
        ];

        $polygon = new Polygon($rings);

        static::assertEquals($expected, $polygon->toArray());
    }

    /**
     * Test a solid polygon from an array of rings.
     */
    public function testSolidPolygonFromArraysGetRings()
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

        $polygon = new Polygon($rings);

        static::assertEquals($expected, $polygon->getRings());
    }

    /**
     * Test a solid polygon from arrays to string.
     */
    public function testSolidPolygonFromArraysToString()
    {
        $expected = '(0 0,10 0,10 10,0 10,0 0),(0 0,10 0,10 10,0 10,0 0)';
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
        $polygon = new Polygon($rings);
        $result = (string) $polygon;

        static::assertEquals($expected, $result);
    }

    /**
     * Test solid polygon from objects to array.
     */
    public function testSolidPolygonFromObjectsToArray()
    {
        $expected = [
            [
                [0, 0],
                [10, 0],
                [10, 10],
                [0, 10],
                [0, 0],
            ],
        ];
        $rings = [
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

        $polygon = new Polygon($rings);

        static::assertEquals($expected, $polygon->toArray());
    }
}
