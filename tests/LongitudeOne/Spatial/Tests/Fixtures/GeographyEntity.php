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
class GeographyEntity implements SingleEntityInterface
{
    #[Column(type: 'geography', nullable: true)]
    protected GeographyInterface $geography;

    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    protected int $id;

    /**
     * Get geography.
     */
    public function getGeography(): GeographyInterface
    {
        return $this->geography;
    }

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set geography.
     *
     * @param GeographyInterface $geography Geography to set
     */
    public function setGeography(GeographyInterface $geography): self
    {
        $this->geography = $geography;

        return $this;
    }
}
