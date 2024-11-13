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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\Standard;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\Tests\Helper\PersistantGeometryHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * ST_MPointFromWkb DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 *
 * @coversDefaultClass
 */
class StMPointFromWkbTest extends PersistOrmTestCase
{
    use PersistantGeometryHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::GEOMETRY_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        $this->supportsPlatform(MySQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testSelect(): void
    {
        $this->persistGeometryO(); // Unused fake point
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT t, ST_AsText(ST_MPointFromWkb(:wkb)) FROM LongitudeOne\Spatial\Tests\Fixtures\GeometryEntity t'
        );
        $query->setParameter('wkb', hex2bin('0104000000030000000101000000000000000000000000000000000000000101000000000000000000F03F000000000000F03F010100000000000000000000400000000000000040'), 'blob');

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertMatchesRegularExpression('|^MULTIPOINT\(|', $result[0][1]);
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testSelectWithSrid(): void
    {
        $this->persistGeometryO(); // Unused fake point
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $dql = 'SELECT t, ST_SRID(ST_MPointFromWkb(:wkb, :srid)) FROM LongitudeOne\Spatial\Tests\Fixtures\GeometryEntity t';
        if ($this->getPlatform() instanceof PostgreSQLPlatform) {
            $dql = 'SELECT t, PgSQL_SRID(ST_MPointFromWkb(:wkb, :srid)) FROM LongitudeOne\Spatial\Tests\Fixtures\GeometryEntity t';
        }

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('wkb', hex2bin('0104000000030000000101000000000000000000000000000000000000000101000000000000000000F03F000000000000F03F010100000000000000000000400000000000000040'), 'blob');
        $query->setParameter('srid', 2154);

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertEquals(2154, $result[0][1]);
    }
}
