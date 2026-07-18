<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
 * Doctrine ORM 2.19 | 3.1
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

namespace LongitudeOne\Spatial\DBAL\Platform;

use LongitudeOne\Spatial\DBAL\Types\AbstractSpatialType;
use LongitudeOne\Spatial\Exception\MissingArgumentException;
use LongitudeOne\Spatial\Exception\UnsupportedTypeException;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;

/**
 * MySql spatial platform.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 */
class MySql extends AbstractPlatform
{
    /**
     * Convert binary data to a php value.
     *
     * @param AbstractSpatialType $type  The spatial type
     * @param SpatialInterface    $value The geometry object
     *
     * @throws UnsupportedTypeException when the provided type is not supported
     */
    public function convertToDatabaseValue(AbstractSpatialType $type, SpatialInterface $value): string
    {
        if (!$type->supportsPlatform($this)) {
            throw new UnsupportedTypeException(sprintf('Platform %s is not currently supported.', $this::class));
        }

        return sprintf('%s(%s)', mb_strtoupper($value->getType()), $value);
    }

    /**
     * Convert to database value.
     *
     * @param AbstractSpatialType $type    The spatial type
     * @param string              $sqlExpr The SQL expression
     */
    public function convertToDatabaseValueSql(AbstractSpatialType $type, $sqlExpr): string
    {
        return sprintf('ST_GeomFromText(%s)', $sqlExpr);
    }

    /**
     * Convert to php value to SQL.
     *
     * @param AbstractSpatialType $type    The spatial type
     * @param string              $sqlExpr The SQL expression
     */
    public function convertToPhpValueSql(AbstractSpatialType $type, $sqlExpr): string
    {
        return sprintf('ST_AsBinary(%s)', $sqlExpr);
    }

    /**
     * Gets the SQL declaration snippet for a spatial function.
     *
     * @param string   $functionName the function name
     * @param string[] $parameters   the function parameters
     *
     * @return string the SQL declaration snippet
     */
    public function getFunctionSqlDeclaration(string $functionName, array $parameters): string
    {
        return match ($functionName) {
            // ST_SetSRID doesn't exists on MySQL, but you can use ST_SRID
            'ST_SetSRID' => parent::getFunctionSqlDeclaration('ST_SRID', $parameters),

            // These are methods, so we add parentheses
            default => parent::getFunctionSqlDeclaration($functionName, $parameters),
        };
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array<string, mixed> $column array SHOULD contain 'type' key
     * @param ?AbstractSpatialType $type   type is now provided
     * @param ?int                 $srid   the srid SHOULD be forwarded when known
     *
     * @throws MissingArgumentException when $column doesn't contain 'type' and AbstractSpatialType is null
     */
    public function getSqlDeclaration(array $column, ?AbstractSpatialType $type = null, ?int $srid = null): string
    {
        $type = parent::checkType($column, $type);

        if (SpatialInterface::GEOGRAPHY === $type->getSQLType()) {
            return 'GEOMETRY';
        }

        return mb_strtoupper($type->getSQLType());
    }
}
