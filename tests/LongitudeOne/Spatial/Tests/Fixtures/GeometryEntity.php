<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2024
 * Copyright Longitude One 2020-2024
 * Copyright 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace LongitudeOne\Spatial\Tests\Fixtures;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use LongitudeOne\Spatial\PHP\Types\Geometry\GeometryInterface;

/**
 * Geometry entity.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @internal
 */
#[Table]
#[Entity]
class GeometryEntity
{
    /**
     * @var GeometryInterface
     */
    #[Column(type: 'geometry', nullable: true)]
    protected $geometry;

    /**
     * @var int
     */
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    protected $id;

    /**
     * Get geometry.
     *
     * @return GeometryInterface
     */
    public function getGeometry()
    {
        return $this->geometry;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set geometry.
     *
     * @param GeometryInterface $geometry geometry to set
     *
     * @return self
     */
    public function setGeometry(GeometryInterface $geometry)
    {
        $this->geometry = $geometry;

        return $this;
    }
}
