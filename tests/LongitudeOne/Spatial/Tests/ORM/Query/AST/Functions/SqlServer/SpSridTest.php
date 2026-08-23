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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\SqlServer;

use Doctrine\DBAL\Platforms\SQLServerPlatform;
use LongitudeOne\Spatial\Tests\Helper\PersistantLineStringHelperTrait;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_SRID SQL Server function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 *
 * @coversDefaultClass
 */
#[Group('sqlserver-only')]
class SpSridTest extends PersistOrmTestCase
{
    use PersistantLineStringHelperTrait;
    use PersistantPointHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->usesEntity(self::GEOGRAPHY_ENTITY);
        $this->supportsPlatform(SQLServerPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testFunctionWithGeography(): void
    {
        $this->persistGeographyLosAngeles();

        $query = $this->getEntityManager()->createQuery(
            'SELECT SqlServer_SRID(g.geography) FROM LongitudeOne\Spatial\Tests\Fixtures\GeographyEntity g'
        );
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertSame('4326', $result[0][1]);
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testFunctionWithGeometry(): void
    {
        $this->createAndPersistGeometricPoint('A', '1', '1', 2154);

        $query = $this->getEntityManager()->createQuery(
            'SELECT SqlServer_SRID(g.point) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity g'
        );
        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertIsArray($result[0]);
        static::assertCount(1, $result[0]);

        static::markTestSkipped('The Longitude One Spatial extension is not yet able to store the SRID in SQL Server. The test is skipped until the feature is implemented.');
        // When SRID will be storable in SQL Server, the following assertion will be valid.
        // Add a test to assert that $result[0][1] is a string equal to '2154'.
    }
}
