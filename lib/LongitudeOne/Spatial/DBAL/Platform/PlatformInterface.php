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

namespace LongitudeOne\Spatial\DBAL\Platform;

use LongitudeOne\Spatial\DBAL\Types\AbstractSpatialType;
use LongitudeOne\Spatial\Exception\MissingArgumentException;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;

/**
 * Spatial platform interface.
 */
interface PlatformInterface
{
    /**
     * Convert Binary to php value.
     *
     * @param AbstractSpatialType  $type    Spatial type
     * @param null|resource|string $sqlExpr Sql expression
     */
    public function convertBinaryToPhpValue(AbstractSpatialType $type, $sqlExpr): SpatialInterface;

    /**
     * Convert string data to a php value.
     *
     * @param AbstractSpatialType $type    The abstract spatial type
     * @param string              $sqlExpr the SQL expression
     */
    public function convertStringToPhpValue(AbstractSpatialType $type, $sqlExpr): SpatialInterface;

    /**
     * Convert to database value.
     *
     * @param AbstractSpatialType $type  The spatial type
     * @param SpatialInterface    $value The geometry or geographic interface
     */
    public function convertToDatabaseValue(AbstractSpatialType $type, SpatialInterface $value): string;

    /**
     * Convert to database value to SQL.
     *
     * @param AbstractSpatialType $type    The spatial type
     * @param string              $sqlExpr The SQL expression
     */
    public function convertToDatabaseValueSql(AbstractSpatialType $type, $sqlExpr): string;

    /**
     * Convert to php value to SQL.
     *
     * @param AbstractSpatialType $type    The spatial type
     * @param string              $sqlExpr The SQL expression
     */
    public function convertToPhpValueSql(AbstractSpatialType $type, $sqlExpr): string;

    /**
     * Get an array of database types that map to this Doctrine type.
     *
     * @param AbstractSpatialType $type the spatial type
     *
     * @return string[]
     */
    public function getMappedDatabaseTypes(AbstractSpatialType $type): array;

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array<string, mixed> $column array SHOULD contain 'type' key
     * @param ?AbstractSpatialType $type   type is now provided
     * @param ?int                 $srid   the srid SHOULD be forwarded when known
     *
     * @throws MissingArgumentException when $column doesn't contain 'type' and AbstractSpatialType is null
     */
    public function getSqlDeclaration(array $column, ?AbstractSpatialType $type = null, ?int $srid = null): string;
}
