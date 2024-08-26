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

use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use LongitudeOne\Spatial\Tests\Helper\PersistantPolygonHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * ST_Envelope DQL function tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 *
 * @coversDefaultClass
 */
class StEnvelopeTest extends PersistOrmTestCase
{
    use PersistantPolygonHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        $this->supportsPlatform(MariaDBPlatform::class);
        $this->supportsPlatform(MySQLPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testSelectStEnvelope(): void
    {
        $this->persistBigPolygon();
        $this->persistHoleyPolygon();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT ST_AsText(ST_Envelope(p.polygon)) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p'
        );
        $result = $query->getResult();

        $expected = 'POLYGON((0 0,0 10,10 10,10 0,0 0))';
        if ($this->getPlatform() instanceof MySQLPlatform) {
            // polygon is equals, but different order
            $expected = 'POLYGON((0 0,10 0,10 10,0 10,0 0))';
        } elseif ($this->getPlatform() instanceof MariaDBPlatform) {
            $expected = 'POLYGON((0 0,10 0,10 10,0 10,0 0))';
        }

        static::assertIsArray($result);
        static::assertEquals($expected, $result[0][1]);
        static::assertEquals($expected, $result[1][1]);
    }

    /**
     * Test a DQL containing function to test in the predicate.
     *
     * @group geometry
     */
    public function testStEnvelopeWhereParameter(): void
    {
        $holeyPolygon = $this->persistHoleyPolygon();
        $this->persistSmallPolygon();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p WHERE ST_Envelope(p.polygon) = ST_GeomFromText(:p)'
        );

        $parameter = 'POLYGON((0 0,0 10,10 10,10 0,0 0))';
        if ($this->getPlatform() instanceof MySQLPlatform) {
            // polygon is equals, but different order
            $parameter = 'POLYGON((0 0,10 0,10 10,0 10,0 0))';
        }

        $query->setParameter('p', $parameter, 'string');

        $result = $query->getResult();

        static::assertIsArray($result);
        if ($this->getPlatform() instanceof MariaDBPlatform) {
            static::assertCount(0, $result);

            return;
        }

        static::assertCount(1, $result);
        static::assertEquals($holeyPolygon, $result[0]);
    }
}
