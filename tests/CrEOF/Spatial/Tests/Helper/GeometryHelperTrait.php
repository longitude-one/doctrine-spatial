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
use CrEOF\Spatial\PHP\Types\Geometry\GeometryInterface;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\GeometryEntity;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;

/**
 * GeometryHelperTrait Trait.
 *
 * This helper provides some methods to generates point entities.
 * All of these points are defined in test documentation.
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
trait GeometryHelperTrait
{
    /**
     * Create a geometric Point entity from an array of points.
     *
     * @param GeometryInterface $geometry object implementing Geometry interface
     *
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createGeometry(GeometryInterface $geometry): GeometryEntity
    {
        $entity = new GeometryEntity();
        $entity->setGeometry($geometry);
        $this->getEntityManager()->persist($entity);

        return $entity;
    }

    /**
     * Create a geometric point at origin.
     *
     * @param int|null $srid Spatial Reference System Identifier
     *
     * @throws DBALException                when credentials fail
     * @throws InvalidValueException        when point is an invalid geometry
     * @throws ORMException                 when cache is not created
     * @throws UnsupportedPlatformException when platform is not supported
     */
    protected function createPointO(int $srid = null): GeometryEntity
    {
        $point = new Point([0, 0]);
        if (null !== $srid) {
            $point->setSrid($srid);
        }

        return $this->createGeometry($point);
    }

    /**
     * Create a geometric straight linestring.
     *
     * @throws InvalidValueException        when linestring is an invalid geometry
     * @throws UnsupportedPlatformException when platform is not supported
     * @throws DBALException                when credentials fail
     * @throws ORMException                 when cache is not created
     */
    protected function createStraightLineString(): GeometryEntity
    {
        $straightLineString = new LineString([
            [1, 1],
            [2, 2],
            [5, 5],
        ]);

        return $this->createGeometry($straightLineString);
    }
}
