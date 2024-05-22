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

namespace LongitudeOne\Spatial\Exception;

/**
 * InvalidValueException class.
 *
 * This exception is thrown when a geometric or geographic value is invalid.
 */
class InvalidValueException extends \Exception implements ExceptionInterface
{
    public const OUT_OF_RANGE_LATITUDE = 'Out of range latitude value, latitude must be between -90 and 90, got "%s".';
    public const OUT_OF_RANGE_LONGITUDE = 'Out of range longitude value, longitude must be between -180 and 180, got "%s".';
    public const OUT_OF_RANGE_MINUTE = 'Out of range minute value, minute must be between 0 and 59, got "%s".';
    public const OUT_OF_RANGE_SECOND = 'Out of range second value, second must be between 0 and 59, got "%s".';
}
