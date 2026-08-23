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

namespace LongitudeOne\Spatial\Exception;

/**
 * Range Exception class.
 *
 * This exception is thrown when a geodesic coordinate is out of range.
 *
 * @internal the library uses this exception internally and is always caught to throw a more explicit InvalidValueException
 */
final class RangeException extends \Exception implements ExceptionInterface
{
}
