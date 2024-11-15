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

namespace LongitudeOne\Spatial\Tests\Helper;

use LongitudeOne\Spatial\PHP\Types\Geography\Point as GeographyPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point as GeometryPoint;
use LongitudeOne\Spatial\Tests\Fixtures\GeographyEntity;
use LongitudeOne\Spatial\Tests\Fixtures\PointEntity as GeometryPointEntity;

/**
 * PolygonHelperTrait Trait.
 *
 * This helper provides some methods to persist geometric and geographic points.
 *
 * @see /docs/Test.rst
 *
 * @method GeographyEntity     persistGeographicPoint(GeographyPoint $point)
 * @method GeometryPointEntity persistGeometricPoint(GeometryPoint $point)
 *
 * @internal
 */
trait PersistantPointHelperTrait
{
    use PointHelperTrait;

    /**
     * Persist a geometry point (x y).
     *
     * @param string $name name of the point
     * @param string $x    coordinate x
     * @param string $y    coordinate y
     * @param ?int   $srid SRID
     */
    protected function createAndPersistGeographicPoint(string $name, string $x, string $y, ?int $srid = null): GeographyEntity
    {
        $point = self::createGeographyPoint($name, $x, $y);
        if (null !== $srid) {
            $point->setSrid($srid);
        }

        return $this->persistGeographicPoint($point);
    }

    /**
     * Persist a geometry point (x y).
     *
     * @param string $name name of the point
     * @param string $x    coordinate x
     * @param string $y    coordinate y
     * @param ?int   $srid SRID
     */
    protected function createAndPersistGeometricPoint(string $name, string $x, string $y, ?int $srid = null): GeometryPointEntity
    {
        $point = self::createGeometryPoint($name, $x, $y);
        if (null !== $srid) {
            $point->setSrid($srid);
        }

        return $this->persistGeometricPoint($point);
    }

    /**
     * Create Dallas geography Point entity and store it in database.
     */
    protected function persistDallasGeography(): GeographyEntity
    {
        return $this->persistGeographicPoint(self::createGeographyPoint('Dallas', '-96.803889', '32.782778'));
    }

    /**
     * Create Dallas geometry Point entity and store it in database.
     */
    protected function persistDallasGeometry(): GeometryPointEntity
    {
        return $this->persistGeometricPoint(self::createGeometryPoint('Dallas', '-96.803889', '32.782778'));
    }

    /**
     * Create Paris city in Lambert93 (French SRID) as geometry Point entity and store it in database.
     *
     * @param bool $setSrid initialize the SRID to 4326 if true
     */
    protected function persistGeographyLosAngeles(bool $setSrid = true): GeographyEntity
    {
        $srid = $setSrid ? 4326 : null;

        return $this->createAndPersistGeographicPoint('Los Angeles', '-118.2430', '34.0522', $srid);
    }

    /**
     * Create Paris city in Lambert93 (French SRID) as geometry Point entity and store it in database.
     *
     * @param bool $setSrid initialize the SRID to 2154 if true
     */
    protected function persistGeometryParisLambert93(bool $setSrid = true): GeometryPointEntity
    {
        $srid = $setSrid ? 2154 : null;

        return $this->createAndPersistGeometricPoint('Paris', '6519', '68624', $srid);
    }

    /**
     * Create Los Angeles geography Point entity and store it in database.
     */
    protected function persistLosAngelesGeography(): GeographyEntity
    {
        return $this->persistGeographicPoint(self::createLosAngelesGeography());
    }

    /**
     * Create Los Angeles geometry Point entity and persist it in database.
     */
    protected function persistLosAngelesGeometry(): GeometryPointEntity
    {
        return $this->persistGeometricPoint($this->createLosAngelesGeometry());
    }

    /**
     * Create New York geography Point entity and store it in database.
     */
    protected function persistNewYorkGeography(): GeographyEntity
    {
        return $this->persistGeographicPoint(self::createNewYorkGeography());
    }

    /**
     * Create New York geometry Point entity and store it in database.
     */
    protected function persistNewYorkGeometry(): GeometryPointEntity
    {
        return $this->persistGeometricPoint(self::createNewYorkGeometry());
    }

    /**
     * Create and persist the point A (1, 2).
     *
     * @param ?int $srid If srid is missing, no SRID is set
     */
    protected function persistPointA(?int $srid = null): GeometryPointEntity
    {
        return $this->createAndPersistGeometricPoint('A', '1', '2', $srid);
    }

    /**
     * Create the point B (-2, 3).
     */
    protected function persistPointB(): GeometryPointEntity
    {
        return $this->createAndPersistGeometricPoint('B', '-2', '3');
    }

    /**
     * Create the point E (5, 5).
     */
    protected function persistPointE(): GeometryPointEntity
    {
        return $this->createAndPersistGeometricPoint('E', '5', '5');
    }

    /**
     * Create the point origin O(0, 0).
     *
     * @param bool $setSrid Set the SRID to zero instead of null
     */
    protected function persistPointO(bool $setSrid = false): GeometryPointEntity
    {
        $srid = $setSrid ? 0 : null;

        return $this->createAndPersistGeometricPoint('O', '0', '0', $srid);
    }

    /**
     * Create Tours city in Lambert93 (French SRID) as geometry Point entity and store it in database.
     *
     * @param bool $setSrid initialize the SRID to 2154 if true
     */
    protected function persistToursLambert93(bool $setSrid = true): GeometryPointEntity
    {
        $srid = $setSrid ? 2154 : null;

        return $this->createAndPersistGeometricPoint('Tours', '525375.21', '6701871.83', $srid);
    }
}
