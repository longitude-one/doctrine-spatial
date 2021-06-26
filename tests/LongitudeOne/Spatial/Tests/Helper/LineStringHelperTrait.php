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

namespace LongitudeOne\Spatial\Tests\Helper;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity;

/**
 * LineStringHelperTrait Trait.
 *
 * This helper provides some methods to generates linestring entities.
 * All of these polygonal geometries are defined in test documentation.
 *
 * Methods beginning with create will store a geo* entity in database.
 *
 * @see /docs/Test.rst
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @method EntityManagerInterface getEntityManager Return the entity interface
 */
trait LineStringHelperTrait
{
    /**
     * Create a broken linestring and persist it in database.
     * Line is created with three aligned points: (3 3) (4 15) (5 22).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws Exception                    when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createAngularLineString(): LineStringEntity
    {
        return $this->createLineString([
            new Point(3, 3),
            new Point(4, 15),
            new Point(5, 22),
        ]);
    }

    /**
     * Create a linestring A and persist it in database.
     * Line is created with two points: (0 0, 10 10).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws Exception                    when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createLineStringA(): LineStringEntity
    {
        return $this->createLineString([
            new Point(0, 0),
            new Point(10, 10),
        ]);
    }

    /**
     * Create a linestring B and persist it in database.
     * Line B crosses lines A and C.
     * Line is created with two points: (0 10, 15 0).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws Exception                    when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createLineStringB(): LineStringEntity
    {
        return $this->createLineString([
            new Point(0, 10),
            new Point(15, 0),
        ]);
    }

    /**
     * Create a linestring C and persist it in database.
     * Linestring C does not cross linestring A.
     * Linestring C crosses linestring B.
     * Line is created with two points: (2 0, 12 10).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws Exception                    when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createLineStringC(): LineStringEntity
    {
        return $this->createLineString([
            new Point(2, 0),
            new Point(12, 10),
        ]);
    }

    /**
     * Create a linestring X and persist it in database.
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws Exception                    when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createLineStringX(): LineStringEntity
    {
        return $this->createLineString([
            new Point(8, 15),
            new Point(4, 8),
        ]);
    }

    /**
     * Create a linestring Y and persist it in database.
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws Exception                    when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createLineStringY(): LineStringEntity
    {
        return $this->createLineString([
            new Point(12, 14),
            new Point(3, 4),
        ]);
    }

    /**
     * Create a linestring Z and persist it in database.
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws Exception                    when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createLineStringZ(): LineStringEntity
    {
        return $this->createLineString([
            new Point(2, 5),
            new Point(3, 6),
            new Point(12, 8),
            new Point(10, 10),
            new Point(13, 11),
        ]);
    }

    /**
     * Create a node linestring and persist it in database.
     * Line is created with three aligned points: (0 0) (1 0) (0 1) (1 1) (0 0).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws Exception                    when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createNodeLineString(): LineStringEntity
    {
        return $this->createLineString([
            new Point(0, 0),
            new Point(1, 0),
            new Point(0, 1),
            new Point(1, 1),
            new Point(0, 0),
        ]);
    }

    /**
     * Create a ring linestring and persist it in database.
     * Line is created with three aligned points: (0 0) (1 0) (1 1) (0 1) (0 0).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws Exception                    when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createRingLineString(): LineStringEntity
    {
        return $this->createLineString([
            new Point(0, 0),
            new Point(1, 0),
            new Point(1, 1),
            new Point(0, 1),
            new Point(0, 0),
        ]);
    }

    /**
     * Create a straight linestring and persist it in database.
     * Line is created with three aligned points: (0 0) (2 2) (5 5).
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws Exception                    when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createStraightLineString(): LineStringEntity
    {
        return $this->createLineString([
            new Point(0, 0),
            new Point(2, 2),
            new Point(5, 5),
        ]);
    }

    /**
     * Create a LineString entity from an array of points.
     *
     * @param Point[] $points the array of points
     *
     * @throws InvalidValueException        when geometries are not valid
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws Exception                    when credentials fail
     * @throws ORMException                 when cache is not created
     */
    private function createLineString(array $points): LineStringEntity
    {
        $lineStringEntity = new LineStringEntity();
        $lineStringEntity->setLineString(new LineString($points));
        $this->getEntityManager()->persist($lineStringEntity);

        return $lineStringEntity;
    }
}
