<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2024
 * Copyright Longitude One 2020-2024
 * Copyright 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace LongitudeOne\Spatial\PHP\Types\Geography;

/**
 * Geography interface for Geography objects.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 */
interface GeographyInterface
{
    public const GEOGRAPHY = 'Geography';

    /**
     * Spatial Reference System Identifier getter.
     *
     * @return int
     */
    public function getSrid();

    /**
     * Type getter.
     *
     * @return string
     */
    public function getType();

    /**
     * Spatial Reference System Identifier setter.
     *
     * @param int $srid A Spatial Reference System Identifier (SRID)
     *
     * @return self
     */
    public function setSrid($srid);
}
