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

namespace LongitudeOne\Spatial\Tests\Unit\DBAL\Types;

use Doctrine\DBAL\Platforms\DB2Platform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Exception\TypeNotRegistered;
use Doctrine\DBAL\Types\Type;
use LongitudeOne\Spatial\DBAL\Platform\PlatformInterface;
use LongitudeOne\Spatial\DBAL\Types\AbstractSpatialType;
use LongitudeOne\Spatial\DBAL\Types\Geometry\PointType;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Abstract spatial Doctrine type tests.
 *
 * @internal
 */
#[CoversClass(AbstractSpatialType::class)]
#[Group('php')]
class AbstractSpatialTypeTest extends TestCase
{
    /**
     * Set up the registered point type.
     */
    protected function setUp(): void
    {
        if (!Type::hasType('point')) {
            Type::addType('point', PointType::class);
        }
    }

    /**
     * Test metadata methods for a registered spatial type.
     */
    public function testMetadataMethods(): void
    {
        $type = new PointType();

        static::assertTrue($type->canRequireSQLConversion());
        static::assertTrue($type->requiresSQLCommentHint(new MySQLPlatform()));
        static::assertSame('point', $type->getName());
        static::assertSame(['point'], $type->getMappedDatabaseTypes(new PostgreSQLPlatform()));
    }

    /**
     * Test an unsupported Doctrine platform.
     */
    public function testRejectsUnsupportedDoctrinePlatform(): void
    {
        $this->expectException(UnsupportedPlatformException::class);
        $this->expectExceptionMessageMatches('/^DBAL platform ".+" is not currently supported\.$/');

        (new PointType())->convertToDatabaseValueSql('point_column', new DB2Platform());
    }

    /**
     * Test an unregistered spatial type.
     */
    public function testUnregisteredTypeName(): void
    {
        $type = new class extends AbstractSpatialType {
            /**
             * @return class-string<PlatformInterface>[]
             */
            protected function getSupportedPlatforms(): array
            {
                return [];
            }
        };

        $this->expectException(TypeNotRegistered::class);
        $this->expectExceptionMessageMatches('/is not currently registered\.$/');

        $type->getName();
    }
}
