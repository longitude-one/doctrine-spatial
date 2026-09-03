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

/**
 * Formats untrusted values for exception messages.
 *
 * @internal
 */
final class InputValueFormatter
{
    /**
     * Format input for use in an exception message.
     *
     * @param string $input input to format
     */
    public static function format(string $input): string
    {
        $input = preg_replace('/[\x00-\x1F\x7F]/', ' ', $input) ?? '';

        if (mb_strlen($input) > 256) {
            return mb_substr($input, 0, 256).'...';
        }

        return $input;
    }
}
