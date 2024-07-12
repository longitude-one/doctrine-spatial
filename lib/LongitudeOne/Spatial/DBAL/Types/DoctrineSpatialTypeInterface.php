<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
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

namespace LongitudeOne\Spatial\DBAL\Types;

use LongitudeOne\Spatial\DBAL\Platform\PlatformInterface;

interface DoctrineSpatialTypeInterface
{
    /**
     * Return (SpatialInterface::GEOGRAPHY|SpatialInterface::GEOMETRY) the family of the type.
     *
     * @return ('Geography'|'Geometry')
     */
    public function getTypeFamily(): string;

    /**
     * Is this type supported by the specified database platform?
     *
     * @param PlatformInterface $platform The specified database platform
     */
    public function supportsPlatform(PlatformInterface $platform): bool;
}
