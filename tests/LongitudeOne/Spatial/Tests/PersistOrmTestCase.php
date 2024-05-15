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

namespace LongitudeOne\Spatial\Tests;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;
use LongitudeOne\Spatial\PHP\Types\Geography\Point as GeographyPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point as GeometryPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\Polygon;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;
use LongitudeOne\Spatial\Tests\Fixtures\GeographyEntity;
use LongitudeOne\Spatial\Tests\Fixtures\LineStringEntity;
use LongitudeOne\Spatial\Tests\Fixtures\PointEntity as GeometryPointEntity;
use LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity;
use LongitudeOne\Spatial\Tests\Fixtures\SingleEntityInterface;

/**
 * This class provides some methods to persist entities then assert that we can select them in database.
 *
 * @see /docs/Test.rst
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 *
 * @method EntityManagerInterface getEntityManager() retrieve entity manager
 *
 * @coversNothing
 */
class PersistOrmTestCase extends OrmTestCase
{
    /**
     * Store and retrieve geography entity in database and retrieve it by its geometry (or geography).
     *
     * Then assert data are equals, not same.
     *
     * @param EntityManagerInterface $entityManager Entity manager to persist data
     * @param object                 $entity        Entity to test
     * @param SpatialInterface       $geo           Geography or geometry object (non-persisted object)
     * @param string                 $method        the method name to retrieve object
     *
     * @return array<int, object> Array of entities declared in Fixtures
     */
    protected static function assertIsRetrievableByGeo(
        EntityManagerInterface $entityManager,
        object $entity,
        SpatialInterface $geo,
        string $method
    ): array {
        $entityManager->persist($entity);
        $entityManager->flush();

        $queryEntity = $entityManager->getRepository(get_class($entity))->{$method}($geo);

        static::assertCount(1, $queryEntity);
        static::assertEquals($entity, $queryEntity[0]);

        return $queryEntity;
    }

    /**
     * Store entity in database and retrieve it by its id.
     *
     * Then assert data are equals, not same.
     *
     * @param EntityManagerInterface $entityManager Entity manager to persist data
     * @param SingleEntityInterface  $entity        Entity to test
     */
    protected static function assertIsRetrievableById(EntityManagerInterface $entityManager, SingleEntityInterface $entity): ?object
    {
        $entityManager->persist($entity);
        $entityManager->flush();

        $id = $entity->getId();

        $queryEntity = $entityManager->getRepository(get_class($entity))->find($id);

        static::assertEquals($entity, $queryEntity);

        return $queryEntity;
    }

    /**
     * Create a geographic Point entity from an array of points.
     *
     * @param GeographyPoint $point Point could be an array of X, Y or an instance of Point class
     */
    protected function persistGeographicPoint(GeographyPoint $point): GeographyEntity
    {
        $pointEntity = new GeographyEntity();
        $pointEntity->setGeography($point);
        $this->getEntityManager()->persist($pointEntity);
        $this->getEntityManager()->flush();

        return $pointEntity;
    }

    /**
     * Create a geometric Point entity from an array of points.
     *
     * @param GeometryPoint $point Point could be an array of X, Y or an instance of Point class
     */
    protected function persistGeometricPoint(GeometryPoint $point): GeometryPointEntity
    {
        $pointEntity = new GeometryPointEntity();
        $pointEntity->setPoint($point);
        $this->getEntityManager()->persist($pointEntity);
        $this->getEntityManager()->flush();

        return $pointEntity;
    }

    /**
     * Create a LineString entity from an array of points.
     *
     * @param LineString $linestring the LineString object to persist
     */
    protected function persistLineString(LineString $linestring): LineStringEntity
    {
        $lineStringEntity = new LineStringEntity();
        $lineStringEntity->setLineString($linestring);
        $this->getEntityManager()->persist($lineStringEntity);
        $this->getEntityManager()->flush();

        return $lineStringEntity;
    }

    /**
     * Persist a polygon.
     *
     * @param Polygon $polygon Geometric polygon to persist
     */
    protected function persistPolygon(Polygon $polygon): PolygonEntity
    {
        try {
            if (!$this->getEntityManager() instanceof EntityManagerInterface) {
                static::fail('The entity manager is unavailable. Did you miss to create when setting up your test?');
            }

            $polygonEntity = new PolygonEntity();
            $polygonEntity->setPolygon($polygon);

            $this->getEntityManager()->persist($polygonEntity);
            $this->getEntityManager()->flush();
        } catch (Exception|ORMException|UnsupportedPlatformException $e) {
            static::fail(sprintf('Unable to persist polygon: %s', $e->getMessage()));
        }

        return $polygonEntity;
    }
}
