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

use LongitudeOne\Spatial\PHP\Types\Geography\Polygon;
use PHPUnit\Framework\TestCase;

/**
 * Polygon geographic object tests.
 *
 * @group php
 *
 * @internal
 *
 * @coversDefaultClass
 */
class PolygonTest extends TestCase
{
    /**
     * Test an empty Polygon.
     */
    public function testGetType(): void
    {
        $polygon = new Polygon([]);
        static::assertEquals('Polygon', $polygon->getType());
    }
}
