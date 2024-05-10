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
use LongitudeOne\Spatial\Exception\MissingArgumentException;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;

/**
 * MySql5.7 and less spatial platform.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 */
class MySql extends AbstractPlatform
{
    /**
     * Convert to database value.
     *
     * @param AbstractSpatialType $type    The spatial type
     * @param string              $sqlExpr The SQL expression
     *
     * @return string
     */
    public function convertToDatabaseValueSql(AbstractSpatialType $type, $sqlExpr)
    {
        return sprintf('ST_GeomFromText(%s)', $sqlExpr);
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
        return sprintf('ST_AsBinary(%s)', $sqlExpr);
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array<string, mixed> $column array SHOULD contain 'type' as key
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
