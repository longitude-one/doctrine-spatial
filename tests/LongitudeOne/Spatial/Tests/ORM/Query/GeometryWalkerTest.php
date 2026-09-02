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

namespace LongitudeOne\Spatial\Tests\ORM\Query;

use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use LongitudeOne\Spatial\ORM\Query\GeometryWalker;
use LongitudeOne\Spatial\PHP\Types\Geometry\Polygon;
use LongitudeOne\Spatial\Tests\Helper\PersistantLineStringHelperTrait;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * GeometryWalker tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @internal
 */
#[CoversClass(GeometryWalker::class)]
#[Group('dql')]
class GeometryWalkerTest extends PersistOrmTestCase
{
    use PersistantLineStringHelperTrait;
    use PersistantPointHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesType('geometry');
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        $this->supportsPlatform(MariaDBPlatform::class);
        $this->supportsPlatform(MySQLPlatform::class);
        parent::setUp();
    }

    /**
     * Start the test.
     *
     * @param EntityManagerInterface $entityManager Entity manager that persists data
     * @param string                 $convert       convert function name (ST_AsBinary, ST_AsText)
     * @param string                 $startPoint    start point function name (ST_StartPoint)
     * @param string                 $envelope      envelope function name (ST_Envelop)
     */
    private static function test(
        EntityManagerInterface $entityManager,
        string $convert,
        string $startPoint,
        string $envelope
    ): void {
        $queryString = sprintf(
            'SELECT %s(%s(l.lineString)) FROM %s l',
            $convert,
            $startPoint,
            self::LINESTRING_ENTITY
        );
        $query = $entityManager->createQuery($queryString);
        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'LongitudeOne\Spatial\ORM\Query\GeometryWalker'
        );

        $result = $query->getResult();
        static::assertIsArray($result);
        static::assertIsArray($result[0]);
        static::assertIsArray($result[1]);
        static::assertEquals(static::createPointOrigin(), $result[0][1]);
        static::assertEquals(static::createPointC(), $result[1][1]);

        $queryString = sprintf(
            'SELECT %s(%s(l.lineString)) FROM %s l',
            $convert,
            $envelope,
            self::LINESTRING_ENTITY
        );
        $query = $entityManager->createQuery($queryString);
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, GeometryWalker::class);

        $result = $query->getResult();
        static::assertIsArray($result);
        static::assertIsArray($result[0]);
        static::assertIsArray($result[1]);
        static::assertInstanceOf(Polygon::class, $result[0][1]);
        static::assertInstanceOf(Polygon::class, $result[1][1]);
    }

    /**
     * Test the geometry walker binary.
     */
    #[Group('geometry')]
    public function testGeometryWalkerBinary(): void
    {
        $this->persistStraightLineString();
        $this->persistAngularLineString();

        self::test($this->getEntityManager(), 'ST_AsBinary', 'ST_StartPoint', 'ST_Envelope');
    }

    /**
     * Test the geometry walker.
     */
    #[Group('geometry')]
    public function testGeometryWalkerText(): void
    {
        $this->persistStraightLineString();
        $this->persistAngularLineString();

        self::test($this->getEntityManager(), 'ST_AsText', 'ST_StartPoint', 'ST_Envelope');
    }
}
