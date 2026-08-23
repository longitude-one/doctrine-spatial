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
use LongitudeOne\Spatial\Tests\Helper\PersistantPolygonHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * SP_MakeEnvelope DQL function tests.
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
class SpMakeEnvelopeTest extends PersistOrmTestCase
{
    use PersistantPolygonHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     */
    #[Group('geometry')]
    public function testSelectStMakeEnvelope(): void
    {
        $this->persistBigPolygon();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_AsText(PgSql_MakeEnvelope(5,5, 10, 10)) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p'
        );
        $result = $query->getResult();

        $expected = [
            [1 => 'POLYGON((5 5,5 10,10 10,10 5,5 5))'],
        ];

        static::assertEquals($expected, $result);
    }
}
