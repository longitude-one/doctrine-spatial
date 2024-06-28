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

namespace LongitudeOne\Spatial\Tests\ORM\Query;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Type;
use LongitudeOne\Spatial\Tests\Helper\PersistantGeometryHelperTrait;
use LongitudeOne\Spatial\Tests\Helper\PersistantPolygonHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * DQL type wrapping tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group php
 *
 * @internal
 *
 * @coversDefaultClass
 */
class WrappingTest extends PersistOrmTestCase
{
    use PersistantGeometryHelperTrait;
    use PersistantPolygonHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->usesEntity(self::GEOMETRY_ENTITY);
        $this->usesType('point');
        $this->supportsPlatform(PostgreSQLPlatform::class);
        $this->supportsPlatform(MySQLPlatform::class);
        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the predicate.
     *
     * @group geometry
     */
    public function testTypeWrappingSelect(): void
    {
        $this->persistBigPolygon();
        $smallPolygon = $this->createSmallPolygon();

        $dql = 'SELECT p, ST_Contains(p.polygon, :geometry) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('geometry', $smallPolygon, 'point');
        $query->processParameterValue('geometry');

        $result = $query->getSQL();

        if (!is_string($result)) {
            static::fail('Unable to get SQL from query');
        }

        try {
            $parameter = Type::getType('point')->convertToDatabaseValueSql('?', $this->getPlatform());
        } catch (Exception $e) {
            static::fail(sprintf('Unable to get type point: %s', $e->getMessage()));
        }

        $regex = preg_quote(sprintf('/.polygon, %s)/', $parameter));

        static::assertMatchesRegularExpression($regex, $result);
    }

    /**
     * @group geometry
     */
    public function testTypeWrappingWhere(): void
    {
        $this->persistGeometryE();

        $query = $this->getEntityManager()->createQuery(
            'SELECT g FROM LongitudeOne\Spatial\Tests\Fixtures\GeometryEntity g WHERE g.geometry = :geometry'
        );

        $query->setParameter('geometry', $this->createGeometryPoint('E', 5, 5), 'point');
        $query->processParameterValue('geometry');

        $result = $query->getSQL();

        if (!is_string($result)) {
            static::fail('Unable to get SQL from query');
        }

        try {
            $parameter = Type::getType('point')->convertToDatabaseValueSql('?', $this->getPlatform());
        } catch (Exception $e) {
            static::fail(sprintf('Unable to get type point: %s', $e->getMessage()));
        }

        $regex = preg_quote(sprintf('/geometry = %s/', $parameter));

        static::assertMatchesRegularExpression($regex, $result);
    }
}
