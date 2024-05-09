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
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
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
        $this->expectExceptionMessage('Invalid coordinate value, got "84:26:46Q".');

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
        $this->expectExceptionMessage('Invalid coordinate value, got "100:56:55P".');

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
     * Test exception on embedded arrays.
     *
     * @throws InvalidValueException This SHALL happen
     */
    public function testEmbeddedArrays(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid parameters passed to LongitudeOne\\Spatial\\PHP\\Types\\Geometry\\Point::__construct: array');

        new Point([[3, []]]);
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
        $this->expectExceptionMessage('Invalid parameters passed to LongitudeOne\\Spatial\\PHP\\Types\\Geometry\\Point::__construct: 5, 5, 5, 5');

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
        $this->expectExceptionMessage('Invalid parameters passed to LongitudeOne\\Spatial\\PHP\\Types\\Geometry\\Point::__construct: array, array, 1234');

        new Point([], [], '1234');
    }

    /**
     * Test setX method with an array.
     *
     * @throws InvalidValueException it should NOT happen
     */
    public function testSetFirstCoordinateWithAnArray(): void
    {
        $point = new Point(10, 10);

        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage('Invalid coordinate value, coordinate cannot be an array.');
        $point->setX('10 20');
    }

    /**
     * Test setY method with an array.
     *
     * @throws InvalidValueException it should NOT happen
     */
    public function testSetSecondCoordinateWithAnArray(): void
    {
        $point = new Point(10, 10);

        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage('Invalid coordinate value, coordinate cannot be an array.');
        $point->setY('10 20');
    }

    /**
     * Test setX method.
     *
     * @throws InvalidValueException it should NOT happen
     */
    public function testSetX(): void
    {
        $point = new Point(10, 10);
        $point->setX('20');
        static::assertSame(20, $point->getX());

        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage('Invalid coordinate value, got "foo".');
        $point->setX('foo');
    }

    /**
     * Test setY method.
     *
     * @throws InvalidValueException it should NOT happen
     */
    public function testSetY(): void
    {
        $point = new Point(10, 10);
        $point->setY('20');
        static::assertSame(20, $point->getLatitude());
        static::assertSame(20, $point->getY());

        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage('Invalid coordinate value, got "foo".');
        $point->setY('foo');
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
        $this->expectExceptionMessage('Invalid parameters passed to LongitudeOne\\Spatial\\PHP\\Types\\Geometry\\Point::__construct: NULL, NULL');

        new Point(null, null);
    }

    /**
     * Test bad string parameters - More than 3 parameters.
     */
    public function testUnusedArguments(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid parameters passed to LongitudeOne\\Spatial\\PHP\\Types\\Geometry\\Point::__construct: 1, 2, 3, 4, NULL, 5');

        new Point(1, 2, 3, 4, null, 5);
    }
}
