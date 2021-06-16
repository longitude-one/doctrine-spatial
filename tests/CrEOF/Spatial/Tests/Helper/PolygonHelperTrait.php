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

namespace CrEOF\Spatial\Tests\Helper;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use CrEOF\Spatial\Tests\Fixtures\PolygonEntity;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

/**
 * TestHelperTrait Trait.
 *
 * This helper provides some methods to generates polygons, linestring and point.
 * All of these polygonal geometries are defined in test documentation.
 *
 * Methods beginning with create will store a geo* entity in database.
 *
 * @see /doc/test.md
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @method EntityManagerInterface getEntityManager Return the entity interface
 */
trait PolygonHelperTrait
{
    /**
     * Create the BIG Polygon and persist it in database.
     * Square (0 0, 10 10).
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     * @throws InvalidValueException        when geometries are not valid
     */
    protected function createBigPolygon(): PolygonEntity
    {
        return $this->createPolygon([
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]),
        ]);
    }

    /**
     * Create an eccentric polygon and persist it in database.
     * Square (6 6, 10 10).
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     * @throws InvalidValueException        when geometries are not valid
     */
    protected function createEccentricPolygon(): PolygonEntity
    {
        return $this->createPolygon([new LineString([
            new Point(6, 6),
            new Point(10, 6),
            new Point(10, 10),
            new Point(6, 10),
            new Point(6, 6),
        ])]);
    }

    /**
     * Create the HOLEY Polygon and persist it in database.
     * (Big polygon minus Small Polygon).
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     * @throws InvalidValueException        when geometries are not valid
     */
    protected function createHoleyPolygon(): PolygonEntity
    {
        return $this->createPolygon([
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 10),
                new Point(0, 10),
                new Point(0, 0),
            ]),
            new LineString([
                new Point(5, 5),
                new Point(7, 5),
                new Point(7, 7),
                new Point(5, 7),
                new Point(5, 5),
            ]),
        ]);
    }

    /**
     * Create the Massachusetts state plane US feet geometry and persist it in database.
     *
     * @param bool $forwardSrid forward SRID for creation
     *
     * @throws DBALException                when credentials fail
     * @throws InvalidValueException        when geometries are not valid
     * @throws ORMException                 when cache is not created
     * @throws UnsupportedPlatformException when platform is not supported
     */
    protected function createMassachusettsState(bool $forwardSrid = true): PolygonEntity
    {
        $srid = null;

        if ($forwardSrid) {
            $srid = 2249;
        }

        return $this->createPolygon([
            new LineString([
                new Point(743238, 2967416),
                new Point(743238, 2967450),
                new Point(743265, 2967450),
                new Point(743265.625, 2967416),
                new Point(743238, 2967416),
            ]),
        ], $srid);
    }

    /**
     * Create the Outer Polygon and persist it in database.
     * Square (15 15, 17 17).
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     * @throws InvalidValueException        when geometries are not valid
     */
    protected function createOuterPolygon(): PolygonEntity
    {
        return $this->createPolygon([
            new LineString([
                new Point(15, 15),
                new Point(17, 15),
                new Point(17, 17),
                new Point(15, 17),
                new Point(15, 15),
            ]),
        ]);
    }

    /**
     * Create the W Polygon and persist it in database.
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     * @throws InvalidValueException        when geometries are not valid
     */
    protected function createPolygonW(): PolygonEntity
    {
        return $this->createPolygon([
            new LineString([
                new Point(0, 0),
                new Point(10, 0),
                new Point(10, 20),
                new Point(0, 20),
                new Point(10, 10),
                new Point(0, 0),
            ]),
        ]);
    }

    /**
     * Create the SMALL Polygon and persist it in database.
     * SQUARE (5 5, 7 7).
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     * @throws InvalidValueException        when geometries are not valid
     */
    protected function createSmallPolygon(): PolygonEntity
    {
        return $this->createPolygon([
            new LineString([
                new Point(5, 5),
                new Point(7, 5),
                new Point(7, 7),
                new Point(5, 7),
                new Point(5, 5),
            ]),
        ]);
    }

    /**
     * Create a Polygon from an array of linestrings.
     *
     * @param array    $lineStrings the array of linestrings
     * @param int|null $srid        Spatial Reference System Identifier
     *
     * @throws DBALException                when credentials fail
     * @throws InvalidValueException        when geometries are not valid
     * @throws ORMException                 when cache is not created
     * @throws UnsupportedPlatformException when platform is not supported
     */
    private function createPolygon(array $lineStrings, int $srid = null): PolygonEntity
    {
        $polygon = new Polygon($lineStrings);
        if (null !== $srid) {
            $polygon->setSrid($srid);
        }

        $polygonEntity = new PolygonEntity();
        $polygonEntity->setPolygon($polygon);

        $this->getEntityManager()->persist($polygonEntity);

        return $polygonEntity;
    }
}
