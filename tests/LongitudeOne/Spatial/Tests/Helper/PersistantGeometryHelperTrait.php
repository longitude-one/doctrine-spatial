<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP          8.1 | 8.2 | 8.3
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

use Doctrine\ORM\EntityManagerInterface;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\PHP\Types\Geometry\GeometryInterface;
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\Tests\Fixtures\GeometryEntity;

/**
 * GeometryHelperTrait Trait.
 *
 * This helper provides some methods to generate geometric entities.
 *
 * @see /docs/Test.rst
 *
 * @method EntityManagerInterface getEntityManager Return the entity interface
 * @method static never fail(string $message = '')
 */
trait PersistantGeometryHelperTrait
{
    use GeometryHelperTrait;

    /**
     * Create a geometric Point entity from an array of points.
     *
     * @param GeometryInterface $geometry object implementing Geometry interface
     */
    protected function persistGeometry(GeometryInterface $geometry): GeometryEntity
    {
        $entity = new GeometryEntity();
        $entity->setGeometry($geometry);
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }

    /**
     * Create a geometric point at A (1 1).
     *
     * @param null|int $srid Spatial Reference System Identifier
     */
    protected function persistGeometryA(?int $srid = null): GeometryEntity
    {
        $point = static::createGeometryPoint('A', 1, 1);
        if (null !== $srid) {
            $point->setSrid($srid);
        }

        return $this->persistGeometry($point);
    }

    /**
     * Create a geometric point E (5 5).
     *
     * @param null|int $srid Spatial Reference System Identifier
     */
    protected function persistGeometryE(?int $srid = null): GeometryEntity
    {
        $point = static::createGeometryPoint('E', 5, 5);
        if (null !== $srid) {
            $point->setSrid($srid);
        }

        return $this->persistGeometry($point);
    }

    /**
     * Create a geometric point at origin.
     *
     * @param null|int $srid Spatial Reference System Identifier
     */
    protected function persistGeometryO(?int $srid = null): GeometryEntity
    {
        $point = static::createGeometryPoint('O', 0, 0);
        if (null !== $srid) {
            $point->setSrid($srid);
        }

        return $this->persistGeometry($point);
    }

    /**
     * Create a straight linestring in a geometry entity.
     */
    protected function persistGeometryStraightLine(): GeometryEntity
    {
        try {
            $straightLineString = new LineString([
                [1, 1],
                [2, 2],
                [5, 5],
            ]);
        } catch (InvalidValueException $e) {
            static::fail(sprintf('Unable to create linestring Y (1 1, 2 2, 5 5): %s', $e->getMessage()));
        }

        return $this->persistGeometry($straightLineString);
    }

    /**
     * Persist an entity with null as geometry.
     */
    protected function persistNullGeometry(): GeometryEntity
    {
        $entity = new GeometryEntity();
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();

        return $entity;
    }
}
