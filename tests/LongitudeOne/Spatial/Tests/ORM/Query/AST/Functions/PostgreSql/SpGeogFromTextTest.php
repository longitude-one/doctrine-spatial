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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\PostgreSql;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * ST_GeogFromText DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @internal
 *
 * @coversDefaultClass
 */
#[Group('dql')]
#[Group('pgsql-only')]
class SpGeogFromTextTest extends PersistOrmTestCase
{
    use PersistantPointHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::GEOGRAPHY_ENTITY);
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the predicate.
     */
    #[Group('geometry')]
    public function testPredicate(): void
    {
        $newYork = $this->persistNewYorkGeography();
        $this->persistLosAngelesGeography();
        $this->persistPointA();

        $query = $this->getEntityManager()->createQuery(
            'SELECT g FROM LongitudeOne\Spatial\Tests\Fixtures\GeographyEntity g WHERE g.geography = PgSQL_GeogFromText(:g)'
        );
        $query->setParameter('g', 'SRID=4326;POINT(-73.938611 40.664167)');

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertEquals($newYork, $result[0]);
    }
}
