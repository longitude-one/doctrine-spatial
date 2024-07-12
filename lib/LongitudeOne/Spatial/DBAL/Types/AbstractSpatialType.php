<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
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

namespace LongitudeOne\Spatial\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Exception\TypeNotRegistered;
use Doctrine\DBAL\Types\Type;
use LongitudeOne\Spatial\DBAL\Platform\MySql;
use LongitudeOne\Spatial\DBAL\Platform\PlatformInterface;
use LongitudeOne\Spatial\DBAL\Platform\PostgreSql;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;
use LongitudeOne\Spatial\PHP\Types\SpatialInterface;

/**
 * Abstract Doctrine GEOMETRY type.
 */
abstract class AbstractSpatialType extends Type implements DoctrineSpatialTypeInterface
{
    // phpcs:disable Generic.NamingConventions.CamelCapsFunctionName.ScopeNotCamelCaps

    /**
     * Does working with this column require SQL conversion functions?
     *
     * This is a metadata function required, for example, in the ORM.
     * Usage of {@link convertToDatabaseValueSql} and
     * {@link convertToPhpValueSql} works for any type and mostly
     * does nothing. This method can additionally be used for optimization.
     *
     * Spatial types require conversion.
     *
     * @return bool
     */
    public function canRequireSQLConversion()
    {
        return true;
    }

    // phpcs:enable

    /**
     * Converts a value from its PHP representation to its database representation of this type.
     *
     * @param mixed            $value    the value to convert
     * @param AbstractPlatform $platform the database platform
     *
     * @throws InvalidValueException        when value is not an instance of Geometry Interface
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof SpatialInterface) {
            throw new InvalidValueException('Spatial column values must implement SpatialInterface');
        }

        return $this->getSpatialPlatform($platform)->convertToDatabaseValue($this, $value);
    }

    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a database value.
     *
     * @param string           $sqlExpr  the SQL expression
     * @param AbstractPlatform $platform the database platform
     *
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    public function convertToDatabaseValueSql($sqlExpr, AbstractPlatform $platform): string
    {
        return $this->getSpatialPlatform($platform)->convertToDatabaseValueSql($this, $sqlExpr);
    }

    // phpcs:disable Generic.NamingConventions.CamelCapsFunctionName.ScopeNotCamelCaps

    /**
     * Converts a value from its database representation to its PHP representation of this type.
     *
     * @param null|resource|string $value    value to convert to PHP
     * @param AbstractPlatform     $platform platform database
     *
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?SpatialInterface
    {
        if (null === $value) {
            return null;
        }

        if (!is_resource($value) && ctype_alpha($value[0])) {
            return $this->getSpatialPlatform($platform)->convertStringToPhpValue($this, $value);
        }

        return $this->getSpatialPlatform($platform)->convertBinaryToPhpValue($this, $value);
    }

    // phpcs:enable

    /**
     * Modifies the SQL expression (identifier, parameter) to convert to a PHP value.
     *
     * @param string           $sqlExpr  SQL expression
     * @param AbstractPlatform $platform platform database
     *
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    public function convertToPhpValueSql($sqlExpr, $platform): string
    {
        return $this->getSpatialPlatform($platform)->convertToPhpValueSql($this, $sqlExpr);
    }

    /**
     * Get an array of database types that map to this Doctrine type.
     *
     * @param AbstractPlatform $platform platform database
     *
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return $this->getSpatialPlatform($platform)->getMappedDatabaseTypes($this);
    }

    /**
     * Gets the name of this type.
     *
     * @return class-string<DoctrineSpatialTypeInterface>
     *
     * @throws TypeNotRegistered When type is not registered in the map
     */
    public function getName()
    {
        /** @var class-string<DoctrineSpatialTypeInterface>|false $className */
        $className = array_search($this::class, self::getTypesMap(), true);

        if (false === $className) {
            throw new TypeNotRegistered(sprintf('Type "%s" is not currently registered.', $this::class));
        }

        return $className;
    }

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array<string,mixed> $column   the field declaration
     * @param AbstractPlatform    $platform database platform
     *
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    public function getSqlDeclaration(array $column, AbstractPlatform $platform): string
    {
        if (!key_exists('srid', $column) || !is_int($column['srid']) || $column['srid'] < 0) {
            $column['srid'] = null;
        }

        return $this->getSpatialPlatform($platform)->getSqlDeclaration($column, $this, $column['srid'] ?? null);
    }

    // phpcs:disable Generic.NamingConventions.CamelCapsFunctionName.ScopeNotCamelCaps

    /**
     * Gets the SQL name of this type.
     *
     * @return string
     */
    public function getSQLType()
    {
        $class = get_class($this);
        $start = mb_strrpos($class, '\\') + 1;
        $len = mb_strlen($class) - $start - 4;

        return mb_substr($class, mb_strrpos($class, '\\') + 1, $len);
    }

    // phpcs:enable

    /**
     * @return (SpatialInterface::GEOGRAPHY|SpatialInterface::GEOMETRY)
     */
    public function getTypeFamily(): string
    {
        return $this instanceof GeographyType ? SpatialInterface::GEOGRAPHY : SpatialInterface::GEOMETRY;
    }

    // phpcs:disable Generic.NamingConventions.CamelCapsFunctionName.ScopeNotCamelCaps

    /**
     * If this Doctrine Type maps to an already mapped database type,
     * reverse schema engineering can't take them apart. You need to mark
     * one of those types as commented, which will have Doctrine use an SQL
     * comment to typehint the actual Doctrine Type.
     *
     * @param AbstractPlatform $platform database platform
     *
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        // TODO onSchemaColumnDefinition event listener?
        return $platform instanceof AbstractPlatform;
    }

    /**
     * Is this type supported by the specified database platform?
     *
     * @param PlatformInterface $platform the spatial platform
     */
    public function supportsPlatform(PlatformInterface $platform): bool
    {
        return in_array($platform::class, $this->getSupportedPlatforms(), true);
    }

    // phpcs:enable

    /**
     * Return the spatial platform when it is accepted.
     *
     * @param AbstractPlatform $platform the database platform
     *
     * @throws UnsupportedPlatformException when platform is unknown by the library
     */
    private function getSpatialPlatform(AbstractPlatform $platform): PlatformInterface
    {
        if ($platform instanceof MySQLPlatform) {
            return new MySql();
        }

        if ($platform instanceof PostgreSQLPlatform) {
            return new PostgreSql();
        }

        throw new UnsupportedPlatformException(sprintf(
            'DBAL platform "%s" is not currently supported.',
            $platform::class
        ));
    }

    /**
     * Return an array of all platform supporting the current type.
     *
     * @return class-string<PlatformInterface>[]
     */
    abstract protected function getSupportedPlatforms(): array;
}
