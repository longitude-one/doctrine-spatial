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

namespace LongitudeOne\Spatial\Tests\DBAL\Platform;

use DG\BypassFinals;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\DB2Platform;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use LongitudeOne\Spatial\DBAL\Platform\PostgreSql;
use LongitudeOne\Spatial\DBAL\Types\Geography\LineStringType;
use LongitudeOne\Spatial\DBAL\Types\Geometry\PointType;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\MissingArgumentException;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;
use LongitudeOne\Spatial\PHP\Types\Geography\Point;
use LongitudeOne\Spatial\Tests\Fixtures\PointEntity;
use LongitudeOne\Spatial\Tests\OrmMockTestCase;

/**
 * Spatial platform tests.
 *
 * @group pgsql-only
 *
 * @internal
 *
 * @covers \LongitudeOne\Spatial\DBAL\Platform\AbstractPlatform
 * @covers \LongitudeOne\Spatial\DBAL\Platform\MySql
 * @covers \LongitudeOne\Spatial\DBAL\Platform\PostgreSql
 */
class PlatformTest extends OrmMockTestCase
{
    /**
     * Set up the test.
     *
     * @throws Exception    When connection failed
     * @throws ORMException when cache is not set
     */
    public function setUp(): void
    {
        BypassFinals::enable();

        if (!Type::hasType('point')) {
            Type::addType('point', PointType::class);
        }

        parent::setUp();
    }

    /**
     * Test SRID returned by getSqlDeclaration.
     *
     * @throws InvalidValueException    this SHALL happen
     * @throws MissingArgumentException this SHOULD NOT happen
     */
    public function testSrid(): void
    {
        $platform = new PostgreSql();
        $expected = 'Geography(LineString,4026)';
        $lineStringType = new LineStringType();
        static::assertSame($expected, $platform->getSqlDeclaration(['srid' => []], $lineStringType, 4026));

        $platform = new PostgreSql();
        static::assertSame($expected, $platform->getSqlDeclaration(['srid' => 4026], $lineStringType));

        self::expectException(InvalidValueException::class);
        self::expectExceptionMessage('SRID SHALL be an integer, but a array is provided');
        $platform->getSqlDeclaration(['srid' => []], $lineStringType);
    }

    /**
     * @throws InvalidValueException    the non-expected exception
     * @throws MissingArgumentException the expected exception
     */
    public function testType(): void
    {
        $platform = new PostgreSql();

        $expected = 'Geometry(Point)';
        $column = ['type' => new PointType()];

        static::assertSame($expected, $platform->getSqlDeclaration($column, new PointType()));
        static::assertSame($expected, $platform->getSqlDeclaration([], new PointType()));
        static::assertSame($expected, $platform->getSqlDeclaration($column));

        self::expectException(MissingArgumentException::class);
        self::expectExceptionMessage('Arguments aren\'t well defined. Please provide a type.');
        $platform->getSqlDeclaration([]);
    }

    /**
     * Test non-supported platform.
     *
     * @throws Exception      when connection failed
     * @throws ORMException   when cache is not set
     * @throws ToolsException this should not happen
     */
    public function testUnsupportedPlatform(): void
    {
        self::expectException(UnsupportedPlatformException::class);
        self::expectExceptionMessageMatches('/^DBAL platform ".+" is not currently supported.$/');

        $metadata = $this->getMockEntityManager()->getClassMetadata(PointEntity::class);
        $schemaTool = new SchemaTool($this->getMockEntityManager());

        $schemaTool->createSchema([$metadata]);
    }

    /**
     * Test non-supported platform.
     *
     * @throws Exception             when connection failed
     * @throws ORMException          when cache is not set
     * @throws ToolsException        this should not happen
     * @throws InvalidValueException this should not happen
     */
    public function testWithUnsupportedPlatform(): void
    {
        self::expectException(UnsupportedPlatformException::class);
        self::expectExceptionMessageMatches('/^DBAL platform ".+" is not currently supported.$/');

        $platform = new DB2Platform();
        $type = new PointType();
        $type->convertToDatabaseValue(new Point(0, 0), $platform);
    }
}
