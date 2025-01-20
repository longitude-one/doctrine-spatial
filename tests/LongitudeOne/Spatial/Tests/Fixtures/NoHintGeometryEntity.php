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

namespace LongitudeOne\Spatial\Tests\Fixtures;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * Geometry entity without type hint.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @internal
 */
#[Table]
#[Entity]
class NoHintGeometryEntity implements SingleEntityInterface
{
    #[Column(type: 'geometry', nullable: true)]
    protected mixed $geometry;

    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    protected int $id;

    /**
     * Get geometry.
     */
    public function getGeometry(): mixed
    {
        return $this->geometry;
    }

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set geometry.
     *
     * @param mixed $geometry the geometry to set
     */
    public function setGeometry(mixed $geometry): self
    {
        $this->geometry = $geometry;

        return $this;
    }
}
