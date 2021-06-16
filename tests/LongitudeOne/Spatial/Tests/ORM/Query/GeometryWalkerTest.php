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

namespace LongitudeOne\Spatial\Tests\ORM\Query;

use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\Tests\Helper\LineStringHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;

/**
 * GeometryWalker tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @coversDefaultClass
 */
class GeometryWalkerTest extends OrmTestCase
{
    use LineStringHelperTrait;

    /**
     * Setup the function type test.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::LINESTRING_ENTITY);
        parent::setUp();
    }

    /**
     * Test the geometry walker binary.
     *
     * @group geometry
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     */
    public function testGeometryWalkerBinary()
    {
        $this->createStraightLineString();
        $this->createAngularLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        switch ($this->getPlatform()->getName()) {
            case 'mysql':
            case 'postgresql':
            default:
                $asBinary = 'ST_AsBinary';
                $startPoint = 'ST_StartPoint';
                $envelope = 'ST_Envelope';
                break;
        }

        $queryString = sprintf(
            'SELECT %s(%s(l.lineString)) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l',
            $asBinary,
            $startPoint
        );
        $query = $this->getEntityManager()->createQuery($queryString);
        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'LongitudeOne\Spatial\ORM\Query\GeometryWalker'
        );

        $result = $query->getResult();
        static::assertEquals(new Point(0, 0), $result[0][1]);
        static::assertEquals(new Point(3, 3), $result[1][1]);

        $queryString = sprintf(
            'SELECT %s(%s(l.lineString)) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l',
            $asBinary,
            $envelope
        );
        $query = $this->getEntityManager()->createQuery($queryString);
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'LongitudeOne\Spatial\ORM\Query\GeometryWalker');

        $result = $query->getResult();
        static::assertInstanceOf('LongitudeOne\Spatial\PHP\Types\Geometry\Polygon', $result[0][1]);
        static::assertInstanceOf('LongitudeOne\Spatial\PHP\Types\Geometry\Polygon', $result[1][1]);
    }

    /**
     * Test the geometry walker.
     *
     * @group geometry
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     */
    public function testGeometryWalkerText()
    {
        $this->createStraightLineString();
        $this->createAngularLineString();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        switch ($this->getPlatform()->getName()) {
            case 'mysql':
            case 'postgresql':
            default:
                $asText = 'ST_AsText';
                $startPoint = 'ST_StartPoint';
                $envelope = 'ST_Envelope';
                break;
        }

        $queryString = sprintf(
            'SELECT %s(%s(l.lineString)) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l',
            $asText,
            $startPoint
        );
        $query = $this->getEntityManager()->createQuery($queryString);
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'LongitudeOne\Spatial\ORM\Query\GeometryWalker');

        $result = $query->getResult();
        static::assertEquals(new Point(0, 0), $result[0][1]);
        static::assertEquals(new Point(3, 3), $result[1][1]);

        $queryString = sprintf(
            'SELECT %s(%s(l.lineString)) FROM LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity l',
            $asText,
            $envelope
        );
        $query = $this->getEntityManager()->createQuery($queryString);
        $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'LongitudeOne\Spatial\ORM\Query\GeometryWalker');

        $result = $query->getResult();
        static::assertInstanceOf('LongitudeOne\Spatial\PHP\Types\Geometry\Polygon', $result[0][1]);
        static::assertInstanceOf('LongitudeOne\Spatial\PHP\Types\Geometry\Polygon', $result[1][1]);
    }
}
