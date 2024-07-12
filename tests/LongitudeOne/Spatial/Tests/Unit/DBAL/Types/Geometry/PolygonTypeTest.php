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

namespace LongitudeOne\Spatial\Tests\Unit\DBAL\Types\Geometry;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\DBAL\Platform\MySql;
use LongitudeOne\Spatial\DBAL\Platform\PostgreSql;
use LongitudeOne\Spatial\DBAL\Types\Geometry\PolygonType;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for the geometric polygon type.
 *
 * @internal
 *
 * @group php
 *
 * @covers \LongitudeOne\Spatial\DBAL\Types\Geometry\PolygonType
 */
class PolygonTypeTest extends TestCase
{
    /**
     * Polygon type.
     */
    private PolygonType $type;

    /**
     * Set up the type for the test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new PolygonType();
    }

    /**
     * Tear down the type.
     */
    protected function tearDown(): void
    {
        unset($this->type);
        parent::tearDown();
    }

    /**
     * Does this type requires SQL comment hint?
     */
    public function testRequiresSqlCommentHint(): void
    {
        static::assertTrue($this->type->requiresSQLCommentHint(new MySQLPlatform()));
        static::assertTrue($this->type->requiresSQLCommentHint(new PostgreSQLPlatform()));
    }

    /**
     * Test each supported platform.
     */
    public function testSupportedPlatform(): void
    {
        static::assertTrue($this->type->supportsPlatform(new MySql()));
        static::assertTrue($this->type->supportsPlatform(new PostgreSql()));
    }

    /**
     * Test the family type.
     */
    public function testTypeFamily(): void
    {
        static::assertSame(SpatialInterface::GEOMETRY, $this->type->getTypeFamily());
    }
}
