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

namespace LongitudeOne\Spatial\PHP\Types\Utils;

/**
 * Moment trait.
 *
 * This trait is used by 4D types to manage the moment.
 *
 * @internal
 */
trait MomentTrait
{
    private const TIMESTAMP = 'U'; // Unix timestamp

    /**
     * Internally, the moment is a date time interface.
     */
    private \DateTimeInterface $moment;

    /**
     * Get the moment.
     */
    public function getMoment(): \DateTimeInterface
    {
        return $this->moment;
    }

    /**
     * Set the moment.
     *
     * @param \DateTimeInterface $moment The moment
     */
    public function setMoment(\DateTimeInterface $moment): self
    {
        $this->moment = $moment;

        return $this;
    }
}
