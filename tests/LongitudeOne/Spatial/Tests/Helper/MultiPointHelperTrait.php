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
use LongitudeOne\Spatial\PHP\Types\Geometry\MultiPoint;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point as GeometryPoint;
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
 * @method EntityManagerInterface getEntityManager the entity interface
 * @internal*
 */
trait MultiPointHelperTrait
{
    /**
     * Create A Multipoint entity entity composed of four points and store it in database.
     *
     * @throws InvalidValueException when geographies are not valid
     */
    protected function createFourPoints(): MultiPointEntity
    {
        $multipoint = new MultiPoint([]);
        $multipoint->addPoint(new GeometryPoint(0, 0));
        $multipoint->addPoint(new GeometryPoint(0, 1));
        $multipoint->addPoint(new GeometryPoint(1, 0));
        $multipoint->addPoint(new GeometryPoint(1, 1));

        return $this->createMultipoint($multipoint);
    }

    /**
     * Create A Multipoint entity entity composed of one point and store it in database.
     *
     * @throws InvalidValueException when geographies are not valid
     */
    protected function createSinglePoint(): MultiPointEntity
    {
        $multipoint = new MultiPoint([]);
        $multipoint->addPoint(new GeometryPoint(0, 0));

        return $this->createMultipoint($multipoint);
    }

    /**
     * Create a geometric MultiPoint entity from an array of geometric points.
     *
     * @param MultiPoint $multipoint Each point could be an array of X, Y or an instance of Point class
     */
    private function createMultipoint(MultiPoint $multipoint): MultiPointEntity
    {
        $multiPointEntity = new MultiPointEntity();
        $multiPointEntity->setMultiPoint($multipoint);
        $this->getEntityManager()->persist($multiPointEntity);

        return $multiPointEntity;
    }
}
