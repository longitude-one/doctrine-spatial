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

namespace LongitudeOne\Spatial\PHP\Types\Internal;

use Doctrine\Deprecations\Deprecation;
use LongitudeOne\Spatial\Exception\InvalidValueException;

/**
 * Normalized arguments for a point constructor.
 *
 * @internal
 */
final readonly class PointConstructorArguments
{
    private const DEPRECATION_LINK = 'https://github.com/longitude-one/doctrine-spatial/issues/81';

    /**
     * @param float|int|string $x    X coordinate or longitude
     * @param float|int|string $y    Y coordinate or latitude
     * @param null|int         $srid Spatial Reference System Identifier
     */
    private function __construct(
        public float|int|string $x,
        public float|int|string $y,
        public ?int $srid,
    ) {
    }

    /**
     * Normalize and validate point constructor arguments.
     *
     * @param mixed[] $arguments  arguments passed to the constructor
     * @param string  $caller     calling method
     * @param string  $pointClass point class name
     *
     * @throws InvalidValueException when an argument is not valid
     */
    public static function from(array $arguments, string $caller, string $pointClass): self
    {
        self::triggerDeprecation($arguments, $caller, $pointClass);

        $count = count($arguments);

        if (1 === $count && is_array($arguments[0])) {
            return self::fromLegacyArray($arguments[0], $caller, $pointClass);
        }

        if (2 === $count) {
            if (is_array($arguments[0])) {
                return self::fromLegacyArray($arguments[0], $caller, $pointClass, true, $arguments[1]);
            }

            if ((is_float($arguments[0]) || is_int($arguments[0]) || is_string($arguments[0]))
                && (is_float($arguments[1]) || is_int($arguments[1]) || is_string($arguments[1]))
            ) {
                return new self($arguments[0], $arguments[1], null);
            }
        }

        if (3 === $count
            && (is_float($arguments[0]) || is_int($arguments[0]) || is_string($arguments[0]))
            && (is_float($arguments[1]) || is_int($arguments[1]) || is_string($arguments[1]))
            && (is_int($arguments[2]) || null === $arguments[2])
        ) {
            return new self($arguments[0], $arguments[1], $arguments[2]);
        }

        throw self::createException($arguments, $caller, $pointClass);
    }

    /**
     * Create an exception describing invalid constructor arguments.
     *
     * @param mixed[] $arguments  invalid arguments
     * @param string  $caller     calling method
     * @param string  $pointClass point class name
     * @param bool    $subArray   whether the invalid arguments are a deprecated coordinate array
     */
    private static function createException(array $arguments, string $caller, string $pointClass, bool $subArray = false): InvalidValueException
    {
        array_walk($arguments, static function (&$value): void {
            if (is_string($value)) {
                $value = InputValueFormatter::format($value);

                return;
            }

            if (is_float($value) || is_int($value)) {
                return;
            }

            $value = gettype($value);
        });

        $message = $subArray
            ? 'Invalid parameters passed to %s::%s: array(%s)'
            : 'Invalid parameters passed to %s::%s: %s';

        return new InvalidValueException(sprintf($message, $pointClass, $caller, implode(', ', $arguments)));
    }

    /**
     * Normalize a deprecated array-based constructor argument.
     *
     * @param mixed[] $coordinates     coordinates passed in an array
     * @param string  $caller          calling method
     * @param string  $pointClass      point class name
     * @param bool    $hasSeparateSrid whether the SRID was passed separately from the coordinates
     * @param mixed   $srid            SRID passed separately from the coordinates
     *
     * @throws InvalidValueException when an argument is not valid
     */
    private static function fromLegacyArray(array $coordinates, string $caller, string $pointClass, bool $hasSeparateSrid = false, mixed $srid = null): self
    {
        if (!array_is_list($coordinates)
            || ($hasSeparateSrid && 2 !== count($coordinates))
            || (!$hasSeparateSrid && (count($coordinates) < 2 || count($coordinates) > 3))
            || !isset($coordinates[0], $coordinates[1])
        ) {
            throw self::createException($coordinates, $caller, $pointClass, true);
        }

        if ((!is_float($coordinates[0]) && !is_int($coordinates[0]) && !is_string($coordinates[0]))
            || (!is_float($coordinates[1]) && !is_int($coordinates[1]) && !is_string($coordinates[1]))
        ) {
            throw self::createException($coordinates, $caller, $pointClass, true);
        }

        if ($hasSeparateSrid) {
            if (!is_int($srid) && null !== $srid) {
                throw self::createException($coordinates, $caller, $pointClass, true);
            }

            return new self($coordinates[0], $coordinates[1], $srid);
        }

        $srid = $coordinates[2] ?? null;
        if (!is_int($srid) && null !== $srid) {
            throw self::createException($coordinates, $caller, $pointClass, true);
        }

        return new self($coordinates[0], $coordinates[1], $srid);
    }

    /**
     * Trigger deprecations for supported legacy constructor forms.
     *
     * @param mixed[] $arguments  arguments passed to the constructor
     * @param string  $caller     calling method
     * @param string  $pointClass point class name
     */
    private static function triggerDeprecation(array $arguments, string $caller, string $pointClass): void
    {
        if (1 === count($arguments) && is_array($arguments[0])) {
            Deprecation::trigger(
                'longitude-one/doctrine-spatial',
                self::DEPRECATION_LINK,
                'Passing an array of coordinates on %s::%s is deprecated since 5.0.2. Please use two arguments instead.',
                $pointClass,
                $caller
            );

            return;
        }

        if (2 === count($arguments) && is_array($arguments[0]) && is_numeric($arguments[1])) {
            Deprecation::trigger(
                'longitude-one/doctrine-spatial',
                self::DEPRECATION_LINK,
                'Passing an array of coordinates and a SRID on %s::%s is deprecated since 5.0.2. Please use three arguments instead.',
                $pointClass,
                $caller
            );
        }
    }

    /**
     * Return the arguments in their former array representation.
     *
     * @return array{0: float|int|string, 1: float|int|string, 2: ?int}
     */
    public function toArray(): array
    {
        return [$this->x, $this->y, $this->srid];
    }
}
