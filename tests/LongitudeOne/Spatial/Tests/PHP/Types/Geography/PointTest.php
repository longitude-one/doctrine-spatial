<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
 * Doctrine ORM 2.19 | 3.1
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2025
 * Copyright Longitude One 2020-2025
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
use LongitudeOne\Spatial\Tests\DataProvider as LoDataProvider;
use PHPUnit\Framework\Attributes\DataProvider;
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
     * @return \Generator<string, array{0: float|int|string}, null, void>
     */
    public static function outOfRangeLatitudeProvider(): \Generator
    {
        foreach (LoDataProvider::outOfRangeLatitudeProvider() as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * @return \Generator<string, array{0: float|int|string}, null, void>
     */
    public static function outOfRangeLongitudeProvider(): \Generator
    {
        foreach (LoDataProvider::outOfRangeLongitudeProvider() as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * Test bad numeric parameters - longitude greater than 180.
     *
     * @throws InvalidValueException it should happen
     */
    public function testBadNumericGreaterThanLongitude(): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage('Out of range longitude value, longitude must be between -180 and 180, got "180.134".');

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
        $this->expectExceptionMessage('Out of range latitude value, latitude must be between -90 and 90, got "-90.00001".');

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
        $this->expectExceptionMessage('Out of range longitude value, longitude must be between -180 and 180, got "-230".');

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
     * Test out-of-range latitude with constructor.
     *
     * @param float|int|string $latitude the out-of-range latitude
     */
    #[DataProvider('outOfRangeLatitudeProvider')]
    public function testOutOfRangeLatitudeConstructor(float|int|string $latitude): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Out of range latitude value, latitude must be between -90 and 90, got "%s".', $latitude));
        new Point(0, $latitude);
    }

    /**
     * Test out of range longitude with constructor.
     *
     * @param float|int|string $longitude the out-of-range longitude
     *
     * @throws InvalidValueException the expected exception
     */
    #[DataProvider('outOfRangeLongitudeProvider')]
    public function testOutOfRangeLongitudeConstructor(float|int|string $longitude): void
    {
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Out of range longitude value, longitude must be between -180 and 180, got "%s".', $longitude));
        new Point($longitude, 0);
    }

    /**
     * Test out of range latitude with latitude setters.
     *
     * @param float|int|string $latitude the out-of-range latitude
     *
     * @throws InvalidValueException the expected exception
     */
    #[DataProvider('outOfRangeLatitudeProvider')]
    public function testOutOfRangeSetLatitude(float|int|string $latitude): void
    {
        $point = new Point(0, 0);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Out of range latitude value, latitude must be between -90 and 90, got "%s".', $latitude));
        $point->setLatitude($latitude);
    }

    /**
     * Test out of range longitude with longitude setters.
     *
     * @param float|int|string $longitude the out-of-range longitude
     *
     * @throws InvalidValueException the expected exception
     */
    #[DataProvider('outOfRangeLongitudeProvider')]
    public function testOutOfRangeSetLongitude(float|int|string $longitude): void
    {
        $point = new Point(0, 0);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Out of range longitude value, longitude must be between -180 and 180, got "%s".', $longitude));
        $point->setLongitude($longitude);
    }

    /**
     * Test out of range longitude.
     *
     * @param float|int|string $longitude the out-of-range longitude
     *
     * @throws InvalidValueException the expected exception
     */
    #[DataProvider('outOfRangeLongitudeProvider')]
    public function testOutOfRangeSetX(float|int|string $longitude): void
    {
        $point = new Point(0, 0);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Out of range longitude value, longitude must be between -180 and 180, got "%s".', $longitude));
        $point->setX($longitude);
    }

    /**
     * Test out of range latitude.
     *
     * @param float|int|string $latitude the out-of-range latitude
     *
     * @throws InvalidValueException the expected exception
     */
    #[DataProvider('outOfRangeLatitudeProvider')]
    public function testOutOfRangeSetY(float|int|string $latitude): void
    {
        $point = new Point(0, 0);
        $this->expectException(InvalidValueException::class);
        $this->expectExceptionMessage(sprintf('Out of range latitude value, latitude must be between -90 and 90, got "%s".', $latitude));
        $point->setY($latitude);
    }
}
