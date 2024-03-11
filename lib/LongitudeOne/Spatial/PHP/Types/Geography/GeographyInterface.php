<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017 - 2022
 * (c) Longitude One 2020 - 2022
 * (c) 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace LongitudeOne\Spatial\PHP\Types\Geography;

use LongitudeOne\Spatial\PHP\Types\SpatialInterface;

/**
 * Geography interface for Geography objects.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 */
interface GeographyInterface extends SpatialInterface
{
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
