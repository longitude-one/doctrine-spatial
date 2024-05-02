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
use LongitudeOne\Spatial\PHP\Types\Geography\GeographyInterface;
use LongitudeOne\Spatial\PHP\Types\Geometry\GeometryInterface;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\PHP\Types\LineStringInterface;
use LongitudeOne\Spatial\PHP\Types\MultiLineStringInterface;
use LongitudeOne\Spatial\PHP\Types\MultiPointInterface;
use LongitudeOne\Spatial\PHP\Types\MultiPolygonInterface;
use LongitudeOne\Spatial\PHP\Types\PointInterface;
use LongitudeOne\Spatial\PHP\Types\PolygonInterface;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;
use LongitudeOne\Spatial\Tests\Helper\PointHelperTrait;
use PHPUnit\Framework\TestCase;

/**
 * Point object tests.
 *
 * @group php
 *
 * @internal
 *
 * @coversDefaultClass
 */
class PointTest extends TestCase
{
    use PointHelperTrait;

    /**
     * Test bad string parameters - latitude degrees greater than 90.
     */
    public function testBadLatitudeDegrees(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[Range Error] Error: Degrees out of range -90 to 90 in value "92:26:46N"');

        new Point('79:56:55W', '92:26:46N');
    }

    /**
     * Test bad string parameters - invalid latitude direction.
     */
    public function testBadLatitudeDirection(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 8: Error: Expected LongitudeOne\\Geo\\String\\Lexer::T_INTEGER or LongitudeOne\\Geo\\String\\Lexer::T_FLOAT, got "Q" in value "84:26:46Q"');

        new Point('100:56:55W', '84:26:46Q');
    }

    /**
     * Test bad string parameters - latitude minutes greater than 59.
     */
    public function testBadLatitudeMinutes(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[Range Error] Error: Minutes greater than 60 in value "84:64:46N"');

        new Point('108:42:55W', '84:64:46N');
    }

    /**
     * Test bad string parameters - latitude seconds greater than 59.
     */
    public function testBadLatitudeSeconds(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[Range Error] Error: Seconds greater than 60 in value "84:23:75N"');

        new Point('108:42:55W', '84:23:75N');
    }

    /**
     * Test bad string parameters - longitude degrees greater than 180.
     */
    public function testBadLongitudeDegrees(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[Range Error] Error: Degrees out of range -180 to 180 in value "190:56:55W"');

        new Point('190:56:55W', '84:26:46N');
    }

    /**
     * Test bad string parameters - invalid longitude direction.
     */
    public function testBadLongitudeDirection(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[Syntax Error] line 0, col 9: Error: Expected LongitudeOne\\Geo\\String\\Lexer::T_INTEGER or LongitudeOne\\Geo\\String\\Lexer::T_FLOAT, got "P" in value "100:56:55P"');

        new Point('100:56:55P', '84:26:46N');
    }

    /**
     * Test bad string parameters - longitude minutes greater than 59.
     */
    public function testBadLongitudeMinutes(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[Range Error] Error: Minutes greater than 60 in value "108:62:55W"');

        new Point('108:62:55W', '84:26:46N');
    }

    /**
     * Test bad string parameters - longitude seconds greater than 59.
     */
    public function testBadLongitudeSeconds(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('[Range Error] Error: Seconds greater than 60 in value "108:53:94W"');

        new Point('108:53:94W', '84:26:46N');
    }

    /**
     * Test getType method.
     */
    public function testGetType(): void
    {
        $point = static::createPointOrigin();
        $result = $point->getType();

        static::assertEquals('Point', $result);
    }

    /**
     * Test a valid numeric point.
     */
    public function testGoodNumericPoint(): void
    {
        $point = $this->createLosAngelesGeometry();

        static::assertEquals(34.0522, $point->getLatitude());
        static::assertEquals(-118.2430, $point->getLongitude());

        try {
            $point
                ->setLatitude('32.782778')
                ->setLongitude('-96.803889')
            ;
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to update geometry point: %s', $e->getMessage()));
        }

        static::assertEquals(32.782778, $point->getLatitude());
        static::assertEquals(-96.803889, $point->getLongitude());
    }

    /**
     * Test valid string points.
     */
    public function testGoodStringPoints(): void
    {
        $point = new Point('79:56:55W', '40:26:46N');

        static::assertEqualsWithDelta(40.44611111111111, $point->getLatitude(), 0.000000000001);
        static::assertEqualsWithDelta(-79.9486111111111, $point->getLongitude(), 0.000000000001);

        $point = new Point('79°56\'55"W', '40°26\'46"N');

        static::assertEqualsWithDelta(40.44611111111111, $point->getLatitude(), 0.000000000001);
        static::assertEqualsWithDelta(-79.9486111111111, $point->getLongitude(), 0.000000000001);

        $point = new Point('79° 56\' 55" W', '40° 26\' 46" N');

        static::assertEqualsWithDelta(40.44611111111111, $point->getLatitude(), 0.000000000001);
        static::assertEqualsWithDelta(-79.9486111111111, $point->getLongitude(), 0.000000000001);

        $point = new Point('79°56′55″W', '40°26′46″N');

        static::assertEqualsWithDelta(40.44611111111111, $point->getLatitude(), 0.000000000001);
        static::assertEqualsWithDelta(-79.9486111111111, $point->getLongitude(), 0.000000000001);

        $point = new Point('79° 56′ 55″ W', '40° 26′ 46″ N');

        static::assertEqualsWithDelta(40.44611111111111, $point->getLatitude(), 0.000000000001);
        static::assertEqualsWithDelta(-79.9486111111111, $point->getLongitude(), 0.000000000001);

        $point = new Point('79:56:55.832W', '40:26:46.543N');

        static::assertEqualsWithDelta(40.446261944444444, $point->getLatitude(), 0.000000000001);
        static::assertEqualsWithDelta(-79.94884222222223, $point->getLongitude(), 0.000000000001);

        $point = new Point('112:4:0W', '33:27:0N');

        static::assertEquals(33.45, $point->getLatitude());
        static::assertEqualsWithDelta(-112.06666666666, $point->getLongitude(), 0.0000000001);
    }

    /**
     * Test interfaces.
     */
    public function testInterface(): void
    {
        $point = new Point(4, 2);

        static::assertInstanceOf(SpatialInterface::class, $point);
        static::assertInstanceOf(GeometryInterface::class, $point);
        static::assertInstanceOf(PointInterface::class, $point);
        static::assertNotInstanceOf(LineStringInterface::class, $point);
        static::assertNotInstanceOf(PolygonInterface::class, $point);
        static::assertNotInstanceOf(MultiPointInterface::class, $point);
        static::assertNotInstanceOf(MultiLineStringInterface::class, $point);
        static::assertNotInstanceOf(MultiPolygonInterface::class, $point);
        static::assertNotInstanceOf(GeographyInterface::class, $point);
    }

    /**
     * Test to convert point to json.
     */
    public function testJson(): void
    {
        $expected = '{"type":"Point","coordinates":[5,5],"srid":null}';
        $point = static::createPointE();

        static::assertEquals($expected, $point->toJson());
        static::assertEquals($expected, json_encode($point));

        $point->setSrid(4326);
        $expected = '{"type":"Point","coordinates":[5,5],"srid":4326}';
        static::assertEquals($expected, $point->toJson());
        static::assertEquals($expected, json_encode($point));
    }

    /**
     * Test bad string parameters - No parameters.
     */
    public function testMissingArguments(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid parameters passed to LongitudeOne\\Spatial\\PHP\\Types\\Geometry\\Point::__construct:');

        new Point();
    }

    /**
     * Test a point created with an array.
     */
    public function testPointFromArrayToString(): void
    {
        $expected = '5 5';
        $point = static::createPointE();

        static::assertSame($expected, (string) $point);
    }

    /**
     * Test error when point is created with too many arguments.
     */
    public function testPointTooManyArguments(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid parameters passed to LongitudeOne\\Spatial\\PHP\\Types\\Geometry\\Point::__construct: "5", "5", "5", "5"');

        new Point(5, 5, 5, 5);
    }

    /**
     * Test point with srid.
     */
    public function testPointWithSrid(): void
    {
        $point = static::createPointWithSrid(2154);
        $result = $point->getSrid();

        static::assertSame(2154, $result);
    }

    /**
     * Test error when point was created with wrong arguments type.
     */
    public function testPointWrongArgumentTypes(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid parameters passed to LongitudeOne\\Spatial\\PHP\\Types\\Geometry\\Point::__construct: Array, Array, "1234"');

        new Point([], [], '1234');
    }

    /**
     * Test to convert a point to an array.
     */
    public function testToArray(): void
    {
        $expected = [0, 0];
        $point = static::createPointOrigin();
        $result = $point->toArray();

        static::assertSame($expected, $result);

        $expected = [-118.243, 34.0522];
        $point = static::createLosAngelesGeometry();
        $result = $point->toArray();

        static::assertSame($expected, $result);
    }

    /**
     * Test bad string parameters - Two invalid parameters.
     */
    public function testTwoInvalidArguments(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid parameters passed to LongitudeOne\\Spatial\\PHP\\Types\\Geometry\\Point::__construct: "", ""');

        new Point(null, null);
    }

    /**
     * Test bad string parameters - More than 3 parameters.
     */
    public function testUnusedArguments(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid parameters passed to LongitudeOne\\Spatial\\PHP\\Types\\Geometry\\Point::__construct: "1", "2", "3", "4", "", "5"');

        new Point(1, 2, 3, 4, null, 5);
    }
}
