<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 7.4 | 8.0
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017 - 2021
 * (c) Longitude One 2020 - 2021
 * (c) 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\Standard;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;
use LongitudeOne\Spatial\Tests\Helper\PolygonHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;

/**
 * ST_Overlaps DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @coversDefaultClass
 */
class StOverlapsTest extends OrmTestCase
{
    use PolygonHelperTrait;

    /**
     * Setup the function type test.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->supportsPlatform('postgresql');
        $this->supportsPlatform('mysql');

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testFunctionInPredicate()
    {
        $bigPolygon = $this->createBigPolygon();
        $this->createSmallPolygon();
        $holeyPolygon = $this->createHoleyPolygon();
        $polygonW = $this->createPolygonW();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT p FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p WHERE ST_Overlaps(p.polygon, ST_GeomFromText(:p)) = true'
            // phpcs:enable
        );
        $query->setParameter('p', 'POLYGON((4 4, 4 12, 12 12, 12 4, 4 4))', 'string');
        $result = $query->getResult();

        static::assertCount(3, $result);
        static::assertEquals($bigPolygon, $result[0]);
        switch ($this->getPlatform()->getName()) {
            case 'mysql':
                //MySQL does not respect the initial polygon and reconstructs it in a bad (direction) way
                break;
            case 'postgresql':
                static::assertEquals($holeyPolygon, $result[1]);
        }
        static::assertEquals($polygonW, $result[2]);
    }

    /**
     * Test a DQL containing function to test.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testFunctionInSelect()
    {
        $bigPolyon = $this->createBigPolygon();
        $smallPolygon = $this->createSmallPolygon();
        $polygonW = $this->createPolygonW();
        $holeyPolygon = $this->createHoleyPolygon();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p, ST_Overlaps(p.polygon, ST_GeomFromText(:p)) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p'
        );
        $query->setParameter('p', 'POLYGON((0 0, 0 12, 12 12, 12 0, 0 0))', 'string');
        $result = $query->getResult();

        static::assertCount(4, $result);
        static::assertEquals($bigPolyon, $result[0][0]);
        static::assertEquals(0, $result[0][1]);
        static::assertEquals($smallPolygon, $result[1][0]);
        static::assertEquals(0, $result[1][1]);
        static::assertEquals($polygonW, $result[2][0]);
        static::assertEquals(1, $result[2][1]);
        static::assertEquals($holeyPolygon, $result[3][0]);
        static::assertEquals(0, $result[3][1]);
    }
}
