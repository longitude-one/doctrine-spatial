<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2024
 * Copyright Longitude One 2020-2024
 * Copyright 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace LongitudeOne\Spatial\Tests\Doctrine;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;

class ConnectionParameters
{
    // phpcs:disable Generic.Files.LineLength.MaxExceeded

    /**
     * Return common connection parameters.
     *
     * @return array{driver: string, user: string, password: string|null, host: string, dbname: string|null, port: string, unix_socket: string|null, driverOptions: array<string, string>}
     */
    public static function getCommonConnectionParameters(): array
    {
        // phpcs:enable Generic.Files.LineLength.MaxExceeded
        $connectionParams = [
            'driver' => $GLOBALS['db_type'],
            'user' => $GLOBALS['db_username'],
            'password' => null,
            'host' => $GLOBALS['db_host'],
            'dbname' => null,
            'port' => $GLOBALS['db_port'],
        ];

        if (isset($GLOBALS['db_name'])) {
            $connectionParams['dbname'] = $GLOBALS['db_name'];
        }

        if (isset($GLOBALS['db_server'])) {
            $connectionParams['server'] = $GLOBALS['db_server'];
        }

        if (!empty($GLOBALS['db_password'])) {
            $connectionParams['password'] = $GLOBALS['db_password'];
        }

        if (isset($GLOBALS['db_unix_socket'])) {
            $connectionParams['unix_socket'] = $GLOBALS['db_unix_socket'];
        }

        if (isset($GLOBALS['db_version'])) {
            $connectionParams['driverOptions']['server_version'] = (string) $GLOBALS['db_version'];
        }

        return $connectionParams;
    }

    /**
     * Return connection parameters.
     *
     * @return array<string, string>
     *
     * @throws Exception when connection is not successful
     */
    public static function getConnectionParameters(): array
    {
        $parameters = static::getCommonConnectionParameters();
        $parameters['dbname'] = static::getAlternateDatabaseName();

        $connection = DriverManager::getConnection($parameters);
        $manager = $connection->createSchemaManager();
        $dbName = $GLOBALS['db_name'];
        $manager->dropDatabase($dbName);
        $manager->createDatabase($dbName);
        $parameters['dbname'] = $dbName;

        return $parameters;
    }

    /**
     * Return connection parameters for alternate database.
     *
     * Alternate database is used with PostgreSQL and doctrine/orm3.0,
     * because we cannot drop database as long as we are connected to it.
     */
    private static function getAlternateDatabaseName(): ?string
    {
        return $GLOBALS['db_alternate'] ?? $GLOBALS['db_name'] ?? static::getCommonConnectionParameters()['dbname'];
    }
}
