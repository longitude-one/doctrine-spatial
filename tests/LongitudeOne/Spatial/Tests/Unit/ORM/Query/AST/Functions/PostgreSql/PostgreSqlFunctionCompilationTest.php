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

namespace LongitudeOne\Spatial\Tests\Unit\ORM\Query\AST\Functions\PostgreSql;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\PostgreSql\SpScale;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\PostgreSql\SpSnapToGrid;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\PostgreSql\SpTransform;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\PostgreSql\SpTranslate;
use LongitudeOne\Spatial\Tests\OrmMockTestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * PostgreSQL spatial DQL function compilation tests.
 *
 * @internal
 */
#[CoversClass(SpScale::class)]
#[CoversClass(SpSnapToGrid::class)]
#[CoversClass(SpTransform::class)]
#[CoversClass(SpTranslate::class)]
#[Group('php')]
#[AllowMockObjectsWithoutExpectations]
class PostgreSqlFunctionCompilationTest extends OrmMockTestCase
{
    /**
     * Register the functions under test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $configuration = $this->getMockEntityManager()->getConfiguration();
        $configuration->addCustomNumericFunction('PgSql_Scale', SpScale::class);
        $configuration->addCustomStringFunction('PgSql_SnapToGrid', SpSnapToGrid::class);
        $configuration->addCustomNumericFunction('PgSql_Transform', SpTransform::class);
        $configuration->addCustomStringFunction('PgSql_Translate', SpTranslate::class);
    }

    /**
     * Test ST_Scale DQL compilation.
     */
    public function testScaleCompilation(): void
    {
        $sql = $this->compile('SELECT PgSql_Scale(l.id, 2, 4) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l');

        static::assertStringContainsString('ST_Scale(', $sql);
    }

    /**
     * Test ST_SnapToGrid DQL compilation for every supported arity.
     */
    public function testSnapToGridCompilation(): void
    {
        foreach ([
            'PgSql_SnapToGrid(l.id, 0.5)',
            'PgSql_SnapToGrid(l.id, 0.5, 1)',
            'PgSql_SnapToGrid(l.id, 5.55, 6.25, 0.5, 0.5)',
            'PgSql_SnapToGrid(l.id, l.id, 0.005, 0.025, 0.5, 0.01)',
        ] as $function) {
            static::assertStringContainsString('ST_SnapToGrid(', $this->compile(sprintf(
                'SELECT %s FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l',
                $function
            )));
        }
    }

    /**
     * Test ST_Transform DQL compilation for both supported arities.
     */
    public function testTransformCompilation(): void
    {
        foreach ([
            'PgSql_Transform(l.id, 4326)',
            'PgSql_Transform(l.id, 2154, 4326)',
        ] as $function) {
            static::assertStringContainsString('ST_Transform(', $this->compile(sprintf(
                'SELECT %s FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l',
                $function
            )));
        }
    }

    /**
     * Test ST_Translate DQL compilation for both supported arities.
     */
    public function testTranslateCompilation(): void
    {
        foreach ([
            'PgSql_Translate(l.id, 4, :y)',
            'PgSql_Translate(l.id, 4, :y, 1)',
        ] as $function) {
            static::assertStringContainsString('ST_Translate(', $this->compile(sprintf(
                'SELECT %s FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l',
                $function
            )));
        }
    }

    /**
     * Use PostgreSQL without opening a database connection.
     */
    protected function getMockConnection(): Connection
    {
        /** @var Driver&MockObject $driver */
        $driver = $this->createMock(Driver::class);
        $driver->method('getDatabasePlatform')->willReturn(new PostgreSQLPlatform());

        return new Connection([], $driver);
    }

    /**
     * Compile DQL without querying a database server.
     *
     * @param string $dql DQL query
     */
    private function compile(string $dql): string
    {
        $sql = $this->getMockEntityManager()->createQuery($dql)->getSQL();

        if (!is_string($sql)) {
            throw new \LogicException('A single DQL select query must compile to a string.');
        }

        return $sql;
    }
}
