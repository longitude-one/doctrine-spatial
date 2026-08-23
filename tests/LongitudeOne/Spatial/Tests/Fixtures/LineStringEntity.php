<?php
/**
 * This file is part of the Doctrine Spatial extension.
 *
 * PHP 8.4 | 8.5
 * Doctrine ORM ^3.6
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2026
 * Copyright Longitude One 2020-2026
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
use LongitudeOne\Spatial\PHP\Types\Geometry\LineString;

/**
 * LineString entity.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @internal
 */
#[Table]
#[Entity]
class LineStringEntity implements SingleEntityInterface
{
    #[Id]
    #[GeneratedValue(strategy: 'AUTO')]
    #[Column(type: 'integer')]
    protected int $id;

    #[Column(type: 'linestring', nullable: true)]
    protected LineString $lineString;

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get lineString.
     */
    public function getLineString(): LineString
    {
        return $this->lineString;
    }

    /**
     * Set lineString.
     *
     * @param LineString $lineString linestring to set
     */
    public function setLineString(LineString $lineString): self
    {
        $this->lineString = $lineString;

        return $this;
    }
}
