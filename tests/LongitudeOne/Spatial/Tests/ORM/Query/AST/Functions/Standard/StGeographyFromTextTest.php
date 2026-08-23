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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\Standard;

use Doctrine\DBAL\Platforms\SQLServerPlatform;
use LongitudeOne\Spatial\Tests\Helper\PersistantLineStringHelperTrait;
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
class StGeographyFromTextTest extends PersistOrmTestCase
{
    use PersistantLineStringHelperTrait;
    use PersistantPointHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::GEOGRAPHY_ENTITY);
        $this->usesEntity(self::GEO_POINT_SRID_ENTITY);

        $this->supportsPlatform(SQLServerPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the predicate.
     */
    #[Group('geometry')]
    public function testPredicate(): void
    {
        $newYork = $this->persistNewYorkGeography();

        $query = $this->getEntityManager()->createQuery(
            'SELECT g FROM LongitudeOne\Spatial\Tests\Fixtures\GeographyEntity g WHERE ST_Equals(ST_GeographyFromText(:g, 4326), ST_GeographyFromText(:g, 4326)) = true'
        );
        $query->setParameter('g', 'POINT(-73.938611 40.664167)');

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertEquals($newYork, $result[0]);
    }
}
