<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2024
 * Copyright Longitude One 2020-2024
 * Copyright 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace LongitudeOne\Spatial\Tests\PHP\Types\Geometry;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geography\GeographyInterface;
use LongitudeOne\Spatial\PHP\Types\Geometry\GeometryInterface;
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\MultiPolygon;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\PHP\Types\Geometry\Polygon;
use LongitudeOne\Spatial\PHP\Types\LineStringInterface;
use LongitudeOne\Spatial\PHP\Types\MultiLineStringInterface;
use LongitudeOne\Spatial\PHP\Types\MultiPointInterface;
use LongitudeOne\Spatial\PHP\Types\MultiPolygonInterface;
use LongitudeOne\Spatial\PHP\Types\PointInterface;
use LongitudeOne\Spatial\PHP\Types\PolygonInterface;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;
use PHPUnit\Framework\TestCase;

/**
 * Polygon object tests.
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
class MultiPolygonTest extends TestCase
{
    /**
     * @throws InvalidValueException this exception should happen
     */
    public function testAddInvalidPolygon(): void
    {
        $expected = 'AbstractMultiPolygon::addPolygon only accepts AbstractPolygon or an array as parameter';

        $polygon = new Polygon(
            [
                new LineString(
                    [
                        new Point(0, 0),
                        new Point(10, 0),
                        new Point(10, 10),
                        new Point(0, 10),
                        new Point(0, 0),
                    ]
                ),
            ]
        );

        $multiPolygon = new MultiPolygon([$polygon]);

        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage($expected);
        $multiPolygon->addPolygon('foo');
    }

    /**
     * Test an empty polygon.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testEmptyMultiPolygon()
    {
        $multiPolygon = new MultiPolygon([]);

        static::assertEmpty($multiPolygon->getPolygons());
    }

    /**
     * Test interfaces.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testInterface()
    {
        $multiPolygon = new MultiPolygon([]);

        static::assertInstanceOf(SpatialInterface::class, $multiPolygon);
        static::assertInstanceOf(GeometryInterface::class, $multiPolygon);
        static::assertInstanceOf(MultiPolygonInterface::class, $multiPolygon);
        static::assertNotInstanceOf(PointInterface::class, $multiPolygon);
        static::assertNotInstanceOf(LineStringInterface::class, $multiPolygon);
        static::assertNotInstanceOf(PolygonInterface::class, $multiPolygon);
        static::assertNotInstanceOf(MultiLineStringInterface::class, $multiPolygon);
        static::assertNotInstanceOf(MultiPointInterface::class, $multiPolygon);
        static::assertNotInstanceOf(GeographyInterface::class, $multiPolygon);
    }

    /**
     * Test to convert multipolygon to Json.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testJson()
    {
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $expected = '{"type":"MultiPolygon","coordinates":[[[[0,0],[10,0],[10,10],[0,10],[0,0]]],[[[5,5],[7,5],[7,7],[5,7],[5,5]]]],"srid":null}';
        // phpcs:enable
        $polygons = [
            [
                [
                    [0, 0],
                    [10, 0],
                    [10, 10],
                    [0, 10],
                    [0, 0],
                ],
            ],
            [
                [
                    [5, 5],
                    [7, 5],
                    [7, 7],
                    [5, 7],
                    [5, 5],
                ],
            ],
        ];
        $multiPolygon = new MultiPolygon($polygons);

        static::assertEquals($expected, $multiPolygon->toJson());
        static::assertEquals($expected, json_encode($multiPolygon));

        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        $expected = '{"type":"MultiPolygon","coordinates":[[[[0,0],[10,0],[10,10],[0,10],[0,0]]],[[[5,5],[7,5],[7,7],[5,7],[5,5]]]],"srid":4326}';
        // phpcs:enable
        $multiPolygon->setSrid(4326);
        static::assertEquals($expected, $multiPolygon->toJson());
        static::assertEquals($expected, json_encode($multiPolygon));
    }

    /**
     * Test to get last polygon from a multipolygon created from a lot objects.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiPolygonFromObjectsGetLastPolygon()
    {
        $firstPolygon = new Polygon(
            [
                new LineString(
                    [
                        new Point(0, 0),
                        new Point(10, 0),
                        new Point(10, 10),
                        new Point(0, 10),
                        new Point(0, 0),
                    ]
                ),
            ]
        );
        $lastPolygon = new Polygon(
            [
                new LineString(
                    [
                        new Point(5, 5),
                        new Point(7, 5),
                        new Point(7, 7),
                        new Point(5, 7),
                        new Point(5, 5),
                    ]
                ),
            ]
        );
        $multiPolygon = new MultiPolygon([$firstPolygon, $lastPolygon]);

        static::assertEquals($lastPolygon, $multiPolygon->getPolygon(-1));
    }

    /**
     * Test to get first polygon from a multipolygon created from a lot objects.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testMultiPolygonFromObjectsGetSinglePolygon()
    {
        $firstPolygon = new Polygon(
            [
                new LineString(
                    [
                        new Point(0, 0),
                        new Point(10, 0),
                        new Point(10, 10),
                        new Point(0, 10),
                        new Point(0, 0),
                    ]
                ),
            ]
        );
        $lastPolygon = new Polygon(
            [
                new LineString(
                    [
                        new Point(5, 5),
                        new Point(7, 5),
                        new Point(7, 7),
                        new Point(5, 7),
                        new Point(5, 5),
                    ]
                ),
            ]
        );
        $multiPolygon = new MultiPolygon([$firstPolygon, $lastPolygon]);

        static::assertEquals($firstPolygon, $multiPolygon->getPolygon(0));
    }

    /**
     * Test getPolygons method.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testSolidMultiPolygonAddPolygon()
    {
        $expected = [
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(0, 0),
                            new Point(10, 0),
                            new Point(10, 10),
                            new Point(0, 10),
                            new Point(0, 0),
                        ]
                    ),
                ]
            ),
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(5, 5),
                            new Point(7, 5),
                            new Point(7, 7),
                            new Point(5, 7),
                            new Point(5, 5),
                        ]
                    ),
                ]
            ),
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(1, 1),
                            new Point(2, 1),
                            new Point(2, 2),
                            new Point(1, 2),
                            new Point(1, 1),
                        ]
                    ),
                ]
            ),
        ];

        $polygon = new Polygon(
            [
                new LineString(
                    [
                        new Point(0, 0),
                        new Point(10, 0),
                        new Point(10, 10),
                        new Point(0, 10),
                        new Point(0, 0),
                    ]
                ),
            ]
        );

        $multiPolygon = new MultiPolygon([$polygon]);

        $multiPolygon->addPolygon(
            [
                [
                    new Point(5, 5),
                    new Point(7, 5),
                    new Point(7, 7),
                    new Point(5, 7),
                    new Point(5, 5),
                ],
            ]
        );

        $polygonObject = new Polygon(
            [
                new LineString(
                    [
                        new Point(1, 1),
                        new Point(2, 1),
                        new Point(2, 2),
                        new Point(1, 2),
                        new Point(1, 1),
                    ]
                ),
            ]
        );

        $multiPolygon->addPolygon($polygonObject);

        static::assertEquals($expected, $multiPolygon->getPolygons());
    }

    /**
     * Test getPolygons method.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testSolidMultiPolygonFromArraysGetPolygons()
    {
        $expected = [
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(0, 0),
                            new Point(10, 0),
                            new Point(10, 10),
                            new Point(0, 10),
                            new Point(0, 0),
                        ]
                    ),
                ]
            ),
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(5, 5),
                            new Point(7, 5),
                            new Point(7, 7),
                            new Point(5, 7),
                            new Point(5, 5),
                        ]
                    ),
                ]
            ),
        ];

        $polygons = [
            [
                [
                    [0, 0],
                    [10, 0],
                    [10, 10],
                    [0, 10],
                    [0, 0],
                ],
            ],
            [
                [
                    [5, 5],
                    [7, 5],
                    [7, 7],
                    [5, 7],
                    [5, 5],
                ],
            ],
        ];

        $multiPolygon = new MultiPolygon($polygons);

        static::assertEquals($expected, $multiPolygon->getPolygons());
    }

    /**
     * Test to convert multipolygon created from array to string.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testSolidMultiPolygonFromArraysToString()
    {
        $expected = '((0 0,10 0,10 10,0 10,0 0)),((5 5,7 5,7 7,5 7,5 5))';
        $polygons = [
            [
                [
                    [0, 0],
                    [10, 0],
                    [10, 10],
                    [0, 10],
                    [0, 0],
                ],
            ],
            [
                [
                    [5, 5],
                    [7, 5],
                    [7, 7],
                    [5, 7],
                    [5, 5],
                ],
            ],
        ];
        $multiPolygon = new MultiPolygon($polygons);
        $result = (string) $multiPolygon;

        static::assertEquals($expected, $result);
    }

    /**
     * Test to convert multipolygon created from objects to array.
     *
     * @throws InvalidValueException This should not happen because of selected value
     */
    public function testSolidMultiPolygonFromObjectsToArray()
    {
        $expected = [
            [
                [
                    [0, 0],
                    [10, 0],
                    [10, 10],
                    [0, 10],
                    [0, 0],
                ],
            ],
            [
                [
                    [5, 5],
                    [7, 5],
                    [7, 7],
                    [5, 7],
                    [5, 5],
                ],
            ],
        ];

        $polygons = [
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(0, 0),
                            new Point(10, 0),
                            new Point(10, 10),
                            new Point(0, 10),
                            new Point(0, 0),
                        ]
                    ),
                ]
            ),
            new Polygon(
                [
                    new LineString(
                        [
                            new Point(5, 5),
                            new Point(7, 5),
                            new Point(7, 7),
                            new Point(5, 7),
                            new Point(5, 5),
                        ]
                    ),
                ]
            ),
        ];

        $multiPolygon = new MultiPolygon($polygons);

        static::assertEquals($expected, $multiPolygon->toArray());
    }
}
