<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1
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
     * @throws Exception when connection is not successful
     */
    public static function getConnectionParameters(): array
    {
        $parameters = static::getCommonConnectionParameters();
        $parameters['dbname'] = $GLOBALS['db_name'];

        $connection = DriverManager::getConnection($parameters);
        $dbName = $connection->getDatabase();

        $connection->close();

        $tmpConnection = DriverManager::getConnection(static::getCommonConnectionParameters());

        $manager = $tmpConnection->createSchemaManager();
        $manager->dropDatabase($dbName);
        $manager->createDatabase($dbName);
        $tmpConnection->close();

        return $parameters;
    }
}