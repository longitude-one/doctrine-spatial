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
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\MissingArgumentException;
use LongitudeOne\Spatial\Exception\UnsupportedTypeException;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;

/**
 * SqlServer spatial platform.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 */
class SqlServer extends AbstractPlatform
{
    /**
     * Default SRID for SQL Server.
     */
    public const DEFAULT_SRID = 4326;

    /**
     * Convert to database value.
     *
     * @param AbstractSpatialType $type  The spatial type
     * @param SpatialInterface    $value The geometry interface
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
     * Convert to database value to SQL.
     *
     * @param AbstractSpatialType $type    The spatial type
     * @param string              $sqlExpr The SQL expression
     */
    public function convertToDatabaseValueSql(AbstractSpatialType $type, $sqlExpr): string
    {
        $type = parent::checkType([], $type);

        if (SpatialInterface::GEOGRAPHY === $type->getSQLType()) {
            return sprintf('geography::STGeomFromText(%s, %d)', $sqlExpr, self::DEFAULT_SRID);
        }

        return sprintf('geometry::STGeomFromText(%s, 0)', $sqlExpr);
    }

    /**
     * Convert to php value to SQL.
     *
     * @param AbstractSpatialType $type    The spatial type
     * @param string              $sqlExpr The SQL expression
     */
    public function convertToPhpValueSql(AbstractSpatialType $type, $sqlExpr): string
    {
        return sprintf('%s.STAsBinary()', $sqlExpr);
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
        $sqlServerFunctionName = str_replace('_', '', $functionName);

        return match ($sqlServerFunctionName) {
            // These are properties, not methods, so we don't add parentheses
            'Lat', 'Long', 'STSrid', 'STX', 'STY' => sprintf('(%s).%s', $parameters[0], $sqlServerFunctionName),

            default => sprintf('(%s).%s(%s)', $parameters[0], $sqlServerFunctionName, implode(', ', array_slice($parameters, 1))),
        };
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array<string,mixed>  $column array SHOULD contain 'type' as key
     * @param ?AbstractSpatialType $type   type is now provided
     * @param ?int                 $srid   the srid SHOULD be forwarded when known
     *
     * @throws MissingArgumentException when $column doesn't contain 'type' and AbstractSpatialType is null
     * @throws InvalidValueException    when SRID is not null nor an integer
     */
    public function getSqlDeclaration(array $column, ?AbstractSpatialType $type = null, ?int $srid = null): string
    {
        $type = parent::checkType($column, $type);

        if (SpatialInterface::GEOGRAPHY === $type->getSQLType()) {
            return 'GEOGRAPHY';
        }

        // SQL Server only supports GEOMETRY and GEOGRAPHY as column types:
        // there are no distinct POINT, LINESTRING, POLYGON, etc. at the schema level.

        return 'GEOMETRY';
    }
}
