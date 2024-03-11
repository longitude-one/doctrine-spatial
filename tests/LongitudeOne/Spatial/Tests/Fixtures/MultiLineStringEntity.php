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
use LongitudeOne\Spatial\PHP\Types\Geometry\MultiLineString;

/**
 * MultiLineString entity.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @internal
 */
#[Table]
#[Entity]
class MultiLineStringEntity
{
    /**
     * @var int
     */
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    protected $id;

    /**
     * @var MultiLineString
     */
    #[Column(type: 'multilinestring', nullable: true)]
    protected $multiLineString;

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
     * Get multiLineString.
     *
     * @return MultiLineString
     */
    public function getMultiLineString()
    {
        return $this->multiLineString;
    }

    /**
     * Set multiLineString.
     *
     * @param MultiLineString $multiLineString multiLineString to set
     *
     * @return self
     */
    public function setMultiLineString(MultiLineString $multiLineString)
    {
        $this->multiLineString = $multiLineString;

        return $this;
    }
}
