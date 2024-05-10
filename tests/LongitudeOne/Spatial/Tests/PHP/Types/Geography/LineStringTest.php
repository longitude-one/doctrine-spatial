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

namespace LongitudeOne\Spatial\Tests\PHP\Types\Geography;

use LongitudeOne\Spatial\PHP\Types\Geography\LineString;
use PHPUnit\Framework\TestCase;

/**
 * LineString geographic object tests.
 *
 * @group php
 *
 * @internal
 *
 * @coversDefaultClass
 */
class LineStringTest extends TestCase
{
    /**
     * Test an empty LineString.
     */
    public function testGetType(): void
    {
        $lineString = new LineString([]);
        static::assertEquals('LineString', $lineString->getType());
    }
}
