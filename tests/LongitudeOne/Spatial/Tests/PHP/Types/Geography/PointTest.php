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

namespace LongitudeOne\Spatial\Tests\PHP\Types\Geography;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geography\Point;
use PHPUnit\Framework\TestCase;

/**
 * Point geographic object tests.
 *
 * @group php
 *
 * @internal
 *
 * @coversDefaultClass
 */
class PointTest extends TestCase
{
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
        $this->expectExceptionMessage('Invalid latitude value, got "84:26:46Q"');

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
        $this->expectExceptionMessage('Invalid longitude value, got "100:56:55P"');

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
     * Test bad numeric parameters - latitude greater than 90.
     *
     * @throws InvalidValueException it should happen
     */
    public function testBadNumericGreaterThanLatitude(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid latitude value "190", must be in range -90 to 90.');

        new Point(55, 190);
    }

    /**
     * Test bad numeric parameters - longitude greater than 180.
     *
     * @throws InvalidValueException it should happen
     */
    public function testBadNumericGreaterThanLongitude(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid longitude value "180.134", must be in range -180 to 180.');

        new Point(180.134, 54);
    }

    /**
     * Test bad numeric parameters - latitude less than -90.
     *
     * @throws InvalidValueException it should happen
     */
    public function testBadNumericLessThanLatitude(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid latitude value "-90.00001", must be in range -90 to 90.');

        new Point(55, -90.00001);
    }

    /**
     * Test bad numeric parameters - longitude less than -180.
     *
     * @throws InvalidValueException it should happen
     */
    public function testBadNumericLessThanLongitude(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid longitude value "-230", must be in range -180 to 180.');

        new Point(-230, 54);
    }

    /**
     * We already have fixed bug19, but we have to verify that a bug won't appear.
     *
     * @throws InvalidValueException It should not happen
     */
    public function testFix19(): void
    {
        $lat = 52.092876;
        $lon = 5.104481;
        $point = new Point($lon, $lat);
        static::assertSame($lon, $point->getLongitude());
        static::assertSame($lat, $point->getLatitude());
    }

    /**
     * Test getType method.
     *
     * @throws InvalidValueException it should NOT happen
     */
    public function testGetType(): void
    {
        $point = new Point(10, 10);
        $result = $point->getType();

        static::assertEquals('Point', $result);
    }

    /**
     * Test a valid numeric point.
     *
     * @throws InvalidValueException it should NOT happen
     */
    public function testGoodNumericPoint(): void
    {
        $point = new Point(-73.7562317, 42.6525793);

        static::assertEquals(42.6525793, $point->getLatitude());
        static::assertEquals(-73.7562317, $point->getLongitude());
    }

    /**
     * Test valid string points.
     */
    public function testGoodStringPoints(): void
    {
        $point = new Point('79:56:55W', '40:26:46N');
        $expected = '{"type":"Point","coordinates":[-79.9486111111111,40.44611111111111],"srid":null}';

        static::assertEqualsWithDelta(40.446111111111, $point->getLatitude(), 0.000000000001);
        static::assertEqualsWithDelta(-79.948611111111, $point->getLongitude(), 0.000000000001);
        static::assertSame($expected, $point->toJson());
        static::assertSame($expected, json_encode($point));

        $point = new Point('79°56\'55"W', '40°26\'46"N');
        $point->setSrid(4326);
        $expected = '{"type":"Point","coordinates":[-79.9486111111111,40.44611111111111],"srid":4326}';

        static::assertEqualsWithDelta(40.446111111111, $point->getLatitude(), 0.000000000001);
        static::assertEqualsWithDelta(-79.948611111111, $point->getLongitude(), 0.000000000001);
        static::assertSame($expected, $point->toJson());
        static::assertSame($expected, json_encode($point));

        $point = new Point('79° 56\' 55" W', '40° 26\' 46" N');

        static::assertEqualsWithDelta(40.446111111111, $point->getLatitude(), 0.000000000001);
        static::assertEqualsWithDelta(-79.948611111111, $point->getLongitude(), 0.000000000001);

        $point = new Point('79°56′55″W', '40°26′46″N');

        static::assertEqualsWithDelta(40.446111111111, $point->getLatitude(), 0.000000000001);
        static::assertEqualsWithDelta(-79.948611111111, $point->getLongitude(), 0.000000000001);

        $point = new Point('79° 56′ 55″ W', '40° 26′ 46″ N');

        static::assertEqualsWithDelta(40.446111111111, $point->getLatitude(), 0.000000000001);
        static::assertEqualsWithDelta(-79.948611111111, $point->getLongitude(), 0.000000000001);

        $point = new Point('79:56:55.832W', '40:26:46.543N');

        static::assertEqualsWithDelta(40.446261944444, $point->getLatitude(), 0.000000000001);
        static::assertEqualsWithDelta(-79.948842222222, $point->getLongitude(), 0.000000000001);

        $point = new Point('112:4:0W', '33:27:0N');

        static::assertEquals(33.45, $point->getLatitude());
        static::assertEqualsWithDelta(-112.06666666666666, $point->getLongitude(), 0.00000000001);
    }

    /**
     * Test a point created with an array and converts to string.
     *
     * @throws InvalidValueException it should NOT happen
     */
    public function testPointFromArrayToString(): void
    {
        $expected = '5 5';
        $point = new Point([5, 5]);

        static::assertEquals($expected, (string) $point);
    }

    /**
     * Test error when point created with too many arguments.
     *
     * @throws InvalidValueException it should happen
     */
    public function testPointTooManyArguments(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid parameters passed to LongitudeOne\\Spatial\\PHP\\Types\\Geography\\Point::__construct: 5, 5, 5, 5');

        new Point(5, 5, 5, 5);
    }

    /**
     * Test a point with SRID.
     *
     * @throws InvalidValueException it should not happen
     */
    public function testPointWithSrid(): void
    {
        $point = new Point(10, 10, 4326);
        $result = $point->getSrid();

        static::assertEquals(4326, $result);

        // Lambert
        $point = new Point(10, 10, 2154);
        $result = $point->getSrid();

        static::assertEquals(2154, $result);
    }

    /**
     * Test error when point is created with wrong arguments.
     *
     * @throws InvalidValueException it should happen
     */
    public function testPointWrongArgumentTypes(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Invalid parameters passed to LongitudeOne\\Spatial\\PHP\\Types\\Geography\\Point::__construct: array, array, 1234');

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
        self::expectExceptionMessage('Invalid longitude value, longitude cannot be an array.');
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
        self::expectExceptionMessage('Invalid latitude value, latitude cannot be an array.');
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
        static::assertSame(20, $point->getLongitude());
        static::assertSame(20, $point->getX());

        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage('Invalid longitude value, got "foo".');
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
        self::expectExceptionMessage('Invalid latitude value, got "foo".');
        $point->setY('foo');
    }

    /**
     * Test to convert point to array.
     *
     * @throws InvalidValueException it should happen
     */
    public function testToArray(): void
    {
        $expected = [10, 10];
        $point = new Point(10, 10);
        $result = $point->toArray();

        static::assertEquals($expected, $result);
    }
}
