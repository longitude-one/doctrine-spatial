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

use Doctrine\ORM\EntityManagerInterface;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geography\Point as GeographyPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point as GeometryPoint;
use LongitudeOne\Spatial\Tests\Fixtures\GeographyEntity;
use LongitudeOne\Spatial\Tests\Fixtures\PointEntity;

/**
 * PointHelperTrait Trait.
 *
 * This helper provides some methods to generates point entities.
 *
 * TODO All of these points will be defined in test documentation.
 * Point Origin (0 0)
 * Point A (1 1)
 * Point B (2 2)
 * Point C (3 3)
 * Point D (4 4)
 * Point E (5 5)
 *
 * Methods beginning with create will create a geo* entity in database, but won't store it in database.
 * Methods beginning with persist will store a geo* entity in database.
 *
 * @see /docs/Test.rst
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @method EntityManagerInterface getEntityManager Return the entity interface
 */
trait PointHelperTrait
{
    /**
     * Create Los Angeles geometry Point entity.
     */
    protected function createLosAngelesGeometry(): GeometryPoint
    {
        try {
            return new GeometryPoint(-118.2430, 34.0522);
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to create Los Angeles Geometry point: %s', $e->getMessage()));
        }
    }

    /**
     * Create Point A (1 1).
     */
    protected function createPointA(): Point
    {
        return $this->createPoint('a', 1, 1);
    }

    /**
     * Create Point B (2 2).
     */
    protected function createPointB(): Point
    {
        return $this->createPoint('B', 2, 2);
    }

    /**
     * Create Point C (3 3).
     */
    protected function createPointC(): Point
    {
        return $this->createPoint('C', 3, 3);
    }

    /**
     * Create Point D (4 4).
     */
    protected function createPointD(): Point
    {
        return $this->createPoint('D', 4, 4);
    }

    /**
     * Create Point E (5 5).
     */
    protected function createPointE(): Point
    {
        return $this->createPoint('E', 5, 5);
    }

    /**
     * Create Point Origin O (0 0).
     */
    protected function createPointOrigin(): Point
    {
        return $this->createPoint('O', 0, 0);
    }

    /**
     * Create Point with SRID.
     */
    protected function createPointWithSrid(int $srid): Point
    {
        try {
            return new Point(5, 5, $srid);
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to create point E (5 5) with srid %d: %s', $srid, $e->getMessage()));
        }
    }

    /**
     * Create Dallas geography Point entity and store it in database.
     *
     * @throws InvalidValueException when geographies are not valid
     */
    protected function persistDallasGeography(): GeographyEntity
    {
        return $this->persistGeography(new GeographyPoint(-96.803889, 32.782778));
    }

    /**
     * Create Dallas geometry Point entity and store it in database.
     *
     * @throws InvalidValueException when geometries are not valid
     */
    protected function persistDallasGeometry(): PointEntity
    {
        return $this->persistGeometry(new GeometryPoint(-96.803889, 32.782778));
    }

    /**
     * Create Los Angeles geography Point entity and store it in database.
     *
     * @throws InvalidValueException when geographies are not valid
     */
    protected function persistLosAngelesGeography(): GeographyEntity
    {
        return $this->persistGeography(new GeographyPoint(-118.2430, 34.0522));
    }

    /**
     * Create Los Angeles geometry Point entity and persist it in database.
     */
    protected function persistLosAngelesGeometry(): PointEntity
    {
        return $this->persistGeometry($this->createLosAngelesGeometry());
    }

    /**
     * Create New York geography Point entity and store it in database.
     *
     * @throws InvalidValueException when geographies are not valid
     */
    protected function persistNewYorkGeography(): GeographyEntity
    {
        return $this->persistGeography(new GeographyPoint(-73.938611, 40.664167));
    }

    /**
     * Create New York geometry Point entity and store it in database.
     *
     * @throws InvalidValueException when geometries are not valid
     */
    protected function persistNewYorkGeometry(): PointEntity
    {
        return $this->persistGeometry(new GeometryPoint(-73.938611, 40.664167));
    }

    /**
     * Create Paris city in Lambert93 (French SRID) as geometry Point entity and store it in database.
     *
     * @param bool $setSrid initialize the SRID to 2154 if true
     */
    protected function persistParisLambert93(bool $setSrid = true): PointEntity
    {
        $pointEntity = $this->persistGeometryPoint('Paris', 6519, 68624);
        if ($setSrid) {
            $pointEntity->getPoint()->setSrid(2154);
        }

        return $pointEntity;
    }

    /**
     * Create and persist the point A (1, 2).
     */
    protected function persistPointA(): PointEntity
    {
        return $this->persistGeometryPoint('A', 1, 2);
    }

    /**
     * Create the point B (-2, 3).
     */
    protected function persistPointB(): PointEntity
    {
        return $this->persistGeometryPoint('B', -2, 3);
    }

    /**
     * Create the point E (5, 5).
     */
    protected function persistPointE(): PointEntity
    {
        return $this->persistGeometryPoint('E', 5, 5);
    }

    /**
     * Create the point origin O(0, 0).
     *
     * @param bool $setSrid Set the SRID to zero instead of null
     */
    protected function persistPointO(bool $setSrid = false): PointEntity
    {
        $geometryEntity = $this->persistGeometryPoint('O', 0, 0);
        if ($setSrid) {
            $geometryEntity->getPoint()->setSrid(0);
        }

        return $geometryEntity;
    }

    /**
     * Create Tours city in Lambert93 (French SRID) as geometry Point entity and store it in database.
     *
     * @param bool $setSrid initialize the SRID to 2154 if true
     */
    protected function persistToursLambert93(bool $setSrid = true): PointEntity
    {
        $pointEntity = $this->persistGeometryPoint('Tours', 525375.21, 6701871.83);
        if ($setSrid) {
            $pointEntity->getPoint()->setSrid(2154);
        }

        return $pointEntity;
    }

    private function createPoint(string $name, float $x, float $y): Point
    {
        try {
            return new Point($x, $y);
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to create point %s(%d %d): %s', $name, $x, $y, $e->getMessage()));
        }
    }

    /**
     * Create a geographic Point entity from an array of points.
     *
     * @param GeographyPoint|array $point Point could be an array of X, Y or an instance of Point class
     */
    private function persistGeography(GeographyPoint $point): GeographyEntity
    {
        $pointEntity = new GeographyEntity();
        $pointEntity->setGeography($point);
        $this->getEntityManager()->persist($pointEntity);

        return $pointEntity;
    }

    /**
     * Create a geometric Point entity from an array of points.
     *
     * @param GeometryPoint|array $point Point could be an array of X, Y or an instance of Point class
     */
    private function persistGeometry(GeometryPoint $point): PointEntity
    {
        $pointEntity = new PointEntity();
        $pointEntity->setPoint($point);
        $this->getEntityManager()->persist($pointEntity);

        return $pointEntity;
    }

    /**
     * Persist a geometry point (x y).
     *
     * @param string $name name of the point
     * @param float  $x    coordinate x
     * @param float  $y    coordinate y
     */
    private function persistGeometryPoint(string $name, float $x, float $y): PointEntity
    {
        return $this->persistGeometry($this->createPoint($name, $x, $y));
    }
}
