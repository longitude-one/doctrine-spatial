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

namespace LongitudeOne\Spatial\DBAL\Platform;

use LongitudeOne\Spatial\DBAL\Types\AbstractSpatialType;
use LongitudeOne\Spatial\DBAL\Types\GeographyType;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\MissingArgumentException;
use LongitudeOne\Spatial\PHP\Types\Geometry\GeometryInterface;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;

/**
 * PostgreSql spatial platform.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 */
class PostgreSql extends AbstractPlatform
{
    public const DEFAULT_SRID = 4326;

    /**
     * Convert Binary to php value.
     *
     * @param AbstractSpatialType $type    Spatial type
     * @param string              $sqlExpr Sql expression
     *
     * @return GeometryInterface
     *
     * @throws InvalidValueException when SQL expression is not a resource
     */
    public function convertBinaryToPhpValue(AbstractSpatialType $type, $sqlExpr)
    {
        if (!is_resource($sqlExpr)) {
            throw new InvalidValueException(sprintf('Invalid resource value "%s"', $sqlExpr));
        }

        $sqlExpr = stream_get_contents($sqlExpr);

        return parent::convertBinaryToPhpValue($type, $sqlExpr);
    }

    /**
     * Convert to database value.
     *
     * @param AbstractSpatialType $type  The spatial type
     * @param SpatialInterface    $value The geometry interface
     *
     * @return string
     */
    public function convertToDatabaseValue(AbstractSpatialType $type, SpatialInterface $value)
    {
        $sridSQL = null;

        if ($type instanceof GeographyType && null === $value->getSrid()) {
            $value->setSrid(self::DEFAULT_SRID);
        }

        $srid = $value->getSrid();
        if (null !== $srid || $type instanceof GeographyType) {
            $sridSQL = sprintf('SRID=%d;', $srid);
        }

        return sprintf('%s%s', $sridSQL, parent::convertToDatabaseValue($type, $value));
    }

    /**
     * Convert to database value to SQL.
     *
     * @param AbstractSpatialType $type    The spatial type
     * @param string              $sqlExpr The SQL expression
     *
     * @return string
     */
    public function convertToDatabaseValueSql(AbstractSpatialType $type, $sqlExpr)
    {
        if ($type instanceof GeographyType) {
            return sprintf('ST_GeographyFromText(%s)', $sqlExpr);
        }

        return sprintf('ST_GeomFromEWKT(%s)', $sqlExpr);
    }

    /**
     * Convert to php value to SQL.
     *
     * @param AbstractSpatialType $type    The spatial type
     * @param string              $sqlExpr The SQL expression
     *
     * @return string
     */
    public function convertToPhpValueSql(AbstractSpatialType $type, $sqlExpr)
    {
        if ($type instanceof GeographyType) {
            return sprintf('ST_AsEWKT(%s)', $sqlExpr);
        }

        return sprintf('ST_AsEWKB(%s)', $sqlExpr);
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array                $column array SHOULD contain 'type' as key
     * @param ?AbstractSpatialType $type   type is now provided
     * @param ?int                 $srid   the srid SHOULD be forwarded when known
     *
     * @return string
     *
     * @throws MissingArgumentException when $column doesn't contain 'type' and AbstractSpatialType is null
     * @throws InvalidValueException    when SRID is not null nor an integer
     */
    public function getSqlDeclaration(array $column, ?AbstractSpatialType $type = null, ?int $srid = null)
    {
        $type = parent::checkType($column, $type);
        $srid = parent::checkSrid($column, $srid);
        $typeFamily = $type->getTypeFamily();
        $sqlType = $type->getSQLType();

        if ($typeFamily === $sqlType) {
            return $sqlType;
        }

        if (null === $srid && key_exists('srid', $column) && null !== $column['srid']) {
            $srid = (int) $column['srid'];
        }

        if (!empty($srid)) {
            return sprintf('%s(%s,%d)', $typeFamily, $sqlType, $srid);
        }

        return sprintf('%s(%s)', $typeFamily, $sqlType);
    }
}
