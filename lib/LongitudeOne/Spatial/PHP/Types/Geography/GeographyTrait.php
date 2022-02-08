<?php

namespace LongitudeOne\Spatial\PHP\Types\Geography;

trait GeographyTrait
{
    /**
     * By default, latitude is before longitude.
     * ISO 6709 standardizes listing the order as latitude, longitude for safety and healthy reasons.
     *
     * @see https://stackoverflow.com/questions/7309121/preferred-order-of-writing-latitude-longitude-tuples-in-gis-services
     *
     * @var bool
     */
    private static bool $latitudeBeforeLongitude = true;

    /**
     * Helper to know orders of Point constructors.
     *
     * @return bool
     */
    public static function isLatitudeBeforeLongitude(): bool
    {
        return static::$latitudeBeforeLongitude;
    }

    /**
     * Helper to know orders of Point constructors.
     *
     * @return bool
     */
    public static function isLongitudeBeforeLatitude(): bool
    {
        return !static::$latitudeBeforeLongitude;
    }

    /**
     * Set order of point constructors.
     * Latitude is now the first parameter, longitude the second.
     */
    public static function setLatitudeBeforeLongitude(): void
    {
        static::$latitudeBeforeLongitude = true;
    }

    /**
     * Set order of point constructors.
     * Latitude is now the first parameter, longitude the second.
     */
    public static function setLongitudeBeforeLatitude(): void
    {
        static::$latitudeBeforeLongitude = false;
    }
}