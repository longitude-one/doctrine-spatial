<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
 * Doctrine ORM 2.19 | 3.1
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2025
 * Copyright Longitude One 2020-2025
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
use LongitudeOne\Spatial\PHP\Types\Geometry\MultiPoint;
use LongitudeOne\Spatial\Tests\Fixtures\MultiPointEntity;

/**
 * MultipointPointHelperTrait Trait.
 *
 * This helper provides some methods to generates multipoint entities.
 * All of these points are defined in test documentation.
 *
 * Methods beginning with create will store a geo* entity in database.
 *
 * @see /docs/Test.rst
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @method        EntityManagerInterface getEntityManager()
 * @method static never                  fail(string $message='')
 *
 * @internal
 */
trait PersistantMultiPointHelperTrait
{
    use PointHelperTrait;

    /**
     * Create A Multipoint entity composed of four points and persist it in database.
     */
    protected function persistFourPoints(): MultiPointEntity
    {
        try {
            $multipoint = new MultiPoint([]);
            $multipoint->addPoint(self::createGeometryPoint('0 0', '0', '0'));
            $multipoint->addPoint(self::createGeometryPoint('0 1', '0', '1'));
            $multipoint->addPoint(self::createGeometryPoint('1 0', '1', '0'));
            $multipoint->addPoint(self::createGeometryPoint('1 1', '0', '1'));
        } catch (InvalidValueException $e) {
            self::fail(sprintf('Unable to create a multipoint (0 0, 0 1, 1 0, 1 1): %s', $e->getMessage()));
        }

        return $this->persistMultiPoint($multipoint);
    }

    /**
     * Create A Multipoint entity composed of one point and persist it in database.
     */
    protected function persistSinglePoint(): MultiPointEntity
    {
        try {
            $multipoint = new MultiPoint([]);
            $multipoint->addPoint(self::createGeometryPoint('0 0', '0', '0'));
        } catch (InvalidValueException $e) {
            self::fail(sprintf('Unable to create a multipoint (0 0): %s', $e->getMessage()));
        }

        return $this->persistMultiPoint($multipoint);
    }

    /**
     * Persist a geometric MultiPoint entity from an array of geometric points.
     *
     * @param MultiPoint $multipoint Each point could be an array of X, Y or an instance of Point class
     */
    private function persistMultiPoint(MultiPoint $multipoint): MultiPointEntity
    {
        $multiPointEntity = new MultiPointEntity();
        $multiPointEntity->setMultiPoint($multipoint);
        $this->getEntityManager()->persist($multiPointEntity);
        $this->getEntityManager()->flush();

        return $multiPointEntity;
    }
}
