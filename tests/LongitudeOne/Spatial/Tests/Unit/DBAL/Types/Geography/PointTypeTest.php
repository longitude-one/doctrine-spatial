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

namespace LongitudeOne\Spatial\Tests\Unit\DBAL\Types\Geography;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\DBAL\Platform\MySql;
use LongitudeOne\Spatial\DBAL\Platform\PostgreSql;
use LongitudeOne\Spatial\DBAL\Types\Geography\PointType;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for the geographic point type.
 *
 * @internal
 *
 * @covers \LongitudeOne\Spatial\DBAL\Types\Geography\PointType
 */
#[Group('php')]
class PointTypeTest extends TestCase
{
    /**
     * Point type.
     */
    private PointType $type;

    /**
     * Set up the type for the test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->type = new PointType();
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
        static::assertSame(SpatialInterface::GEOGRAPHY, $this->type->getTypeFamily());
    }
}
