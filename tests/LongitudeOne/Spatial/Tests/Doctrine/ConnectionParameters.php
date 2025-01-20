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

namespace LongitudeOne\Spatial\Tests\Doctrine;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;

class ConnectionParameters
{
    /**
     * Return common connection parameters.
     *
     * @return array{driver: ('ibm_db2'|'mysqli'|'oci8'|'pdo_mysql'|'pdo_oci'|'pdo_pgsql'|'pdo_sqlite'|'pdo_sqlsrv'|'pgsql'|'sqlite3'|'sqlsrv'), user: string, password: string, host: string, dbname: null|string, port: int, unix_socket?: string, driverOptions?: array<string, string>}
     */
    public static function getCommonConnectionParameters(): array
    {
        // phpcs:enable Generic.Files.LineLength.MaxExceeded
        $connectionParams = [
            'driver' => self::checkDriver(),
            'user' => $GLOBALS['db_username'],
            'password' => '',
            'host' => $GLOBALS['db_host'],
            'dbname' => 'main',
            'port' => (int) $GLOBALS['db_port'],
        ];

        if (null !== $GLOBALS['db_name']) {
            $connectionParams['dbname'] = $GLOBALS['db_name'];
        }

        if (isset($GLOBALS['db_server'])) {
            $connectionParams['server'] = (string) $GLOBALS['db_server'];
        }

        if (!empty($GLOBALS['db_password'])) {
            $connectionParams['password'] = (string) $GLOBALS['db_password'];
        }

        if (isset($GLOBALS['db_unix_socket'])) {
            $connectionParams['unix_socket'] = $GLOBALS['db_unix_socket'];
        }

        if (isset($GLOBALS['db_version'])) {
            $connectionParams['driverOptions']['server_version'] = $GLOBALS['db_version'];
        }

        return $connectionParams;
    }

    /**
     * Return connection parameters.
     *
     * @return array{driver: ('ibm_db2'|'mysqli'|'oci8'|'pdo_mysql'|'pdo_oci'|'pdo_pgsql'|'pdo_sqlite'|'pdo_sqlsrv'|'pgsql'|'sqlite3'|'sqlsrv'), user: string, password: string, host: string, dbname: string, port: int, unix_socket?: string, driverOptions?: array<string, string>}
     *
     * @throws Exception when connection is not successful
     */
    public static function getConnectionParameters(): array
    {
        $parameters = self::getCommonConnectionParameters();
        $parameters['dbname'] = self::getAlternateDatabaseName();

        $connection = DriverManager::getConnection($parameters);
        $manager = $connection->createSchemaManager();
        $dbName = (string) $GLOBALS['db_name'];
        $manager->dropDatabase($dbName);
        $manager->createDatabase($dbName);
        $parameters['dbname'] = $dbName;

        return $parameters;
    }

    /**
     * @return ('ibm_db2'|'mysqli'|'oci8'|'pdo_mysql'|'pdo_oci'|'pdo_pgsql'|'pdo_sqlite'|'pdo_sqlsrv'|'pgsql'|'sqlite3'|'sqlsrv') driver
     */
    private static function checkDriver(): string
    {
        $drivers = DriverManager::getAvailableDrivers();

        if (in_array($GLOBALS['db_type'], $drivers)) {
            return $GLOBALS['db_type'];
        }

        throw new \InvalidArgumentException(sprintf('Driver %s is not available.', $GLOBALS['driver']));
    }

    /**
     * Return connection parameters for alternate database.
     *
     * Alternate database is used with PostgreSQL and doctrine/orm3.0,
     * because we cannot drop database as long as we are connected to it.
     */
    private static function getAlternateDatabaseName(): string
    {
        return $GLOBALS['db_alternate'] ?? $GLOBALS['db_name'] ?? self::getCommonConnectionParameters()['dbname'] ?? 'main';
    }
}
