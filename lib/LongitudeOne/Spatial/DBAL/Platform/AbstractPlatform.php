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

use LongitudeOne\Geo\WKB\Exception\ExceptionInterface;
use LongitudeOne\Geo\WKB\Parser as BinaryParser;
use LongitudeOne\Geo\WKT\Parser as StringParser;
use LongitudeOne\Spatial\DBAL\Types\AbstractSpatialType;
use LongitudeOne\Spatial\DBAL\Types\DoctrineSpatialTypeInterface;
use LongitudeOne\Spatial\DBAL\Types\GeographyType;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\MissingArgumentException;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;

/**
 * Abstract spatial platform.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre-tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 */
abstract class AbstractPlatform implements PlatformInterface
{
    /**
     * Check both arguments and return srid when possible.
     *
     * @param array<string, mixed> $column array MAY contain 'srid' as key
     * @param ?int                 $srid   srid MAY be provided
     *
     * @throws InvalidValueException when SRID is not null nor an integer
     */
    protected static function checkSrid(array $column, ?int $srid): ?int
    {
        $srid ??= $column['srid'] ?? null;

        if (null !== $srid && !is_int($srid)) {
            $message = sprintf(
                'SRID SHALL be an integer, but a %s is provided',
                gettype($srid)
            );

            throw new InvalidValueException($message);
        }

        return $srid;
    }

    /**
     * Check both argument and return AbstractSpatialType when possible.
     *
     * @param array<string, mixed> $column array SHOULD contain 'type' as key
     * @param ?AbstractSpatialType $type   type is now provided
     *
     * @throws MissingArgumentException when $column doesn't contain 'type' and AbstractSpatialType is null
     */
    protected static function checkType(array $column, ?AbstractSpatialType $type): AbstractSpatialType
    {
        $type ??= $column['type'] ?? null;

        if (!$type instanceof AbstractSpatialType) {
            throw new MissingArgumentException('Arguments aren\'t well defined. Please provide a type.');
        }

        return $type;
    }

    /**
     * Convert binary data to a php value.
     *
     * @param DoctrineSpatialTypeInterface $type    The abstract spatial type
     * @param resource|string              $sqlExpr the SQL expression
     *
     * @return SpatialInterface
     *
     * @throws ExceptionInterface|InvalidValueException when the provided type is not supported
     */
    public function convertBinaryToPhpValue(DoctrineSpatialTypeInterface $type, $sqlExpr)
    {
        if (is_resource($sqlExpr)) {
            $sqlExpr = stream_get_contents($sqlExpr);
        }

        if (false === $sqlExpr) {
            throw new InvalidValueException('Invalid resource value.');
        }

        $parser = new BinaryParser($sqlExpr);

        return $this->newObjectFromValue($type, $parser->parse());
    }

    /**
     * Convert string data to a php value.
     *
     * @param AbstractSpatialType $type    The abstract spatial type
     * @param string              $sqlExpr the SQL expression
     *
     * @return SpatialInterface
     *
     * @throws InvalidValueException when the provided type is not supported
     */
    public function convertStringToPhpValue(AbstractSpatialType $type, $sqlExpr)
    {
        $parser = new StringParser($sqlExpr);

        return $this->newObjectFromValue($type, $parser->parse());
    }

    // phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundInImplementedInterfaceBeforeLastUsed

    /**
     * Convert binary data to a php value.
     *
     * @param AbstractSpatialType $type  The spatial type
     * @param SpatialInterface    $value The geometry object
     *
     * @return string
     */
    public function convertToDatabaseValue(AbstractSpatialType $type, SpatialInterface $value)
    {
        // the unused variable $type is used by overriding method
        return sprintf('%s(%s)', mb_strtoupper($value->getType()), $value);
    }

    // phpcs:enable

    /**
     * Get an array of database types that map to this Doctrine type.
     *
     * @param AbstractSpatialType $type the spatial type
     *
     * @return string[]
     */
    public function getMappedDatabaseTypes(AbstractSpatialType $type)
    {
        $sqlType = mb_strtolower($type->getSQLType());

        if ($type instanceof GeographyType && 'geography' !== $sqlType) {
            $sqlType = sprintf('geography(%s)', $sqlType);
        }

        return [$sqlType];
    }

    /**
     * Create spatial object from parsed value.
     *
     * @param DoctrineSpatialTypeInterface                  $type  The type spatial type
     * @param array{type: string, srid?: ?int, value:mixed} $value The value of the spatial object
     *
     * @throws InvalidValueException when the provided type is not supported
     */
    private function newObjectFromValue(DoctrineSpatialTypeInterface $type, $value): SpatialInterface
    {
        $typeFamily = $type->getTypeFamily();
        $typeName = mb_strtoupper($value['type']);

        $constName = sprintf('%s::%s', SpatialInterface::class, $typeName);

        if (!defined($constName)) {
            throw new InvalidValueException(sprintf('Unsupported %s type "%s".', $typeFamily, $typeName));
        }

        /** @var string $constValue */
        $constValue = constant($constName);

        /** @var class-string<SpatialInterface> $class */
        $class = sprintf('LongitudeOne\Spatial\PHP\Types\%s\%s', $typeFamily, $constValue);

        if (isset($value['srid'])) {
            return new $class($value['value'], $value['srid']);
        }

        return new $class($value['value']);
    }
}
