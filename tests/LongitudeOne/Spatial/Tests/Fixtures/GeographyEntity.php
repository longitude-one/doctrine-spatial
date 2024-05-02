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

namespace LongitudeOne\Spatial\Tests\Fixtures;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use LongitudeOne\Spatial\PHP\Types\Geography\GeographyInterface;

/**
 * Geography entity.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @internal
 */
#[Entity]
class GeographyEntity
{
    /**
     * @var GeographyInterface
     */
    #[Column(type: 'geography', nullable: true)]
    protected $geography;

    /**
     * @var int
     */
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    protected $id;

    /**
     * Get geography.
     *
     * @return GeographyInterface
     */
    public function getGeography()
    {
        return $this->geography;
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
     * Set geography.
     *
     * @param GeographyInterface $geography Geography to set
     *
     * @return self
     */
    public function setGeography(GeographyInterface $geography)
    {
        $this->geography = $geography;

        return $this;
    }
}
