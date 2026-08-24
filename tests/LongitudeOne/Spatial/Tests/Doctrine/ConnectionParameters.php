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

namespace LongitudeOne\Spatial\Tests\Doctrine;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;

class ConnectionParameters
{
    /**
     * Return common connection parameters.
     *
     * @return array{driver: ('ibm_db2'|'mysqli'|'oci8'|'pdo_mysql'|'pdo_oci'|'pdo_pgsql'|'pdo_sqlite'|'pdo_sqlsrv'|'pgsql'|'sqlite3'|'sqlsrv'), user: string, password: string, host: string, dbname: string, port: int, server?: string, unix_socket?: string, driverOptions?: array<string, string>}
     */
    public static function getCommonConnectionParameters(): array
    {
        // phpcs:enable Generic.Files.LineLength.MaxExceeded
        return [
            'driver' => self::checkDriver(),
            ...self::getValidatedConnectionParameters(),
        ];
    }

    /**
     * Return connection parameters.
     *
     * @return array{driver: ('ibm_db2'|'mysqli'|'oci8'|'pdo_mysql'|'pdo_oci'|'pdo_pgsql'|'pdo_sqlite'|'pdo_sqlsrv'|'pgsql'|'sqlite3'|'sqlsrv'), user: string, password: string, host: string, dbname: string, port: int, server?: string, unix_socket?: string, driverOptions?: array<string, string>}
     *
     * @throws Exception when connection is not successful
     */
    public static function getConnectionParameters(): array
    {
        $parameters = self::getCommonConnectionParameters();
        $dbName = $parameters['dbname'];
        $parameters['dbname'] = self::getAlternateDatabaseName();

        $connection = DriverManager::getConnection($parameters);
        $manager = $connection->createSchemaManager();

        // Drop the database if it exists and create a new one
        foreach ($manager->listDatabases() as $database) {
            if ($database === $dbName) {
                $manager->dropDatabase($dbName);

                break;
            }
        }

        $manager->createDatabase($dbName);
        $parameters['dbname'] = $dbName;

        return $parameters;
    }

    /**
     * @return ('ibm_db2'|'mysqli'|'oci8'|'pdo_mysql'|'pdo_oci'|'pdo_pgsql'|'pdo_sqlite'|'pdo_sqlsrv'|'pgsql'|'sqlite3'|'sqlsrv') driver
     */
    private static function checkDriver(): string
    {
        $driver = self::getRequiredStringParameter('db_type');

        return match ($driver) {
            'ibm_db2' => 'ibm_db2',
            'mysqli' => 'mysqli',
            'oci8' => 'oci8',
            'pdo_mysql' => 'pdo_mysql',
            'pdo_oci' => 'pdo_oci',
            'pdo_pgsql' => 'pdo_pgsql',
            'pdo_sqlite' => 'pdo_sqlite',
            'pdo_sqlsrv' => 'pdo_sqlsrv',
            'pgsql' => 'pgsql',
            'sqlite3' => 'sqlite3',
            'sqlsrv' => 'sqlsrv',
            default => throw new \InvalidArgumentException(sprintf('Driver %s is not available.', $driver)),
        };
    }

    /**
     * Return connection parameters for alternate database.
     *
     * Alternate database is used with PostgreSQL and doctrine/orm3.0,
     * because we cannot drop database as long as we are connected to it.
     */
    private static function getAlternateDatabaseName(): string
    {
        return self::getOptionalStringParameter('db_alternate')
            ?? self::getOptionalStringParameter('db_name')
            ?? self::getCommonConnectionParameters()['dbname'];
    }

    /**
     * Return database driver options.
     *
     * @return array<string, string>
     */
    private static function getDriverOptions(): array
    {
        $driverOptions = self::getOptionalStringParameter('db_driver_options');
        if (null === $driverOptions) {
            return [];
        }

        $options = [];
        foreach (explode(',', $driverOptions) as $option) {
            $keyValue = explode('=', $option, 2);
            if (2 !== count($keyValue)) {
                throw new \InvalidArgumentException('The db_driver_options parameter must use key=value pairs.');
            }

            [$key, $value] = $keyValue;
            $options[$key] = $value;
        }

        return $options;
    }

    /**
     * Return an optional string global parameter.
     */
    private static function getOptionalStringParameter(string $name): ?string
    {
        if (!isset($GLOBALS[$name])) {
            return null;
        }

        if (!is_string($GLOBALS[$name])) {
            throw new \InvalidArgumentException(sprintf('The %s parameter must be a string.', $name));
        }

        return $GLOBALS[$name];
    }

    /**
     * Return the validated database port.
     */
    private static function getPort(): int
    {
        $port = filter_var($GLOBALS['db_port'] ?? null, FILTER_VALIDATE_INT);
        if (false === $port) {
            throw new \InvalidArgumentException('The db_port parameter must be an integer.');
        }

        return $port;
    }

    /**
     * Return a required string global parameter.
     */
    private static function getRequiredStringParameter(string $name): string
    {
        $value = self::getOptionalStringParameter($name);
        if (null === $value) {
            throw new \InvalidArgumentException(sprintf('The %s parameter must be a string.', $name));
        }

        return $value;
    }

    /**
     * Return connection parameter values after validating their global inputs.
     *
     * @return array{user: string, password: string, host: string, dbname: string, port: int, server?: string, unix_socket?: string, driverOptions?: array<string, string>}
     */
    private static function getValidatedConnectionParameters(): array
    {
        $parameters = [
            'user' => self::getRequiredStringParameter('db_username'),
            'password' => self::getOptionalStringParameter('db_password') ?? '',
            'host' => self::getRequiredStringParameter('db_host'),
            'dbname' => self::getOptionalStringParameter('db_name') ?? 'main',
            'port' => self::getPort(),
        ];

        foreach (['db_server' => 'server', 'db_unix_socket' => 'unix_socket'] as $globalName => $parameterName) {
            $value = self::getOptionalStringParameter($globalName);
            if (null !== $value) {
                $parameters[$parameterName] = $value;
            }
        }

        $serverVersion = self::getOptionalStringParameter('db_version');
        $driverOptions = self::getDriverOptions();
        if (null !== $serverVersion || [] !== $driverOptions) {
            $parameters['driverOptions'] = $driverOptions;
            if (null !== $serverVersion) {
                $parameters['driverOptions']['server_version'] = $serverVersion;
            }
        }

        return $parameters;
    }
}
