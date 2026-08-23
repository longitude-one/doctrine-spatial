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

namespace LongitudeOne\Spatial\Tests;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * Common test code.
 */
abstract class OrmMockTestCase extends SpatialTestCase
{
    private const FIXTURES_PATH = __DIR__.'/Fixtures';

    protected EntityManagerInterface $mockEntityManager;

    /**
     * Set up the mocked entity manager.
     *
     * @throws Exception    when connection is not successful
     * @throws ORMException when cache is not set
     */
    protected function setUp(): void
    {
        $this->mockEntityManager = $this->getMockEntityManager();
    }

    /**
     * Return the mocked connection.
     */
    protected function getMockConnection(): Connection
    {
        /** @var Driver|MockObject $driver */
        $driver = $this->getMockBuilder(Driver\PDO\SQLite\Driver::class)
            ->onlyMethods(['getDatabasePlatform'])
            ->getMock()
        ;

        $platformClass = null;

        // Doctrine ORM ^2.19
        if (class_exists('\Doctrine\DBAL\Platforms\SqlitePlatform')) {
            $platformClass = '\Doctrine\DBAL\Platforms\SqlitePlatform';
        }

        // Doctrine ORM ^3.0
        if (class_exists('\Doctrine\DBAL\Platforms\SQLitePlatform')) {
            $platformClass = '\Doctrine\DBAL\Platforms\SQLitePlatform';
        }

        if (null === $platformClass) {
            static::fail('Test cannot be performed, no SQLite platform found');
        }

        $platform = new $platformClass();

        $driver->method('getDatabasePlatform')
            ->willReturn($platform)
        ;

        return new Connection([], $driver);
    }

    /**
     * Get the mocked entity manager.
     *
     * @return EntityManagerInterface a mocked entity manager
     */
    protected function getMockEntityManager(): EntityManagerInterface
    {
        if (isset($this->mockEntityManager)) {
            return $this->mockEntityManager;
        }

        $path = [0 => realpath(self::FIXTURES_PATH)];
        if (false === $path[0]) {
            static::fail(sprintf('Test cannot be launched because the %s directory does not exist.', self::FIXTURES_PATH));
        }
        $config = new Configuration();

        $config->setMetadataCache(new ArrayAdapter());

        // Preferred order for lazy loading:
        // 1. Native lazy objects when Doctrine supports them on PHP 8.4+
        // 2. Legacy proxy configuration as a fallback for older Doctrine versions
        // 3. Leave Doctrine defaults untouched if neither API is available
        if (method_exists($config, 'enableNativeLazyObjects') && PHP_VERSION_ID >= 80400) {
            // Doctrine 3.5+ added native lazy objects, and they are only supported on PHP 8.4+.
            $config->enableNativeLazyObjects(true);
        } elseif (method_exists($config, 'setProxyDir') && method_exists($config, 'setProxyNamespace')) {
            // This is the legacy proxy-based fallback used when native lazy objects are not available.
            $config->setProxyDir(__DIR__.'/Proxies');
            $config->setProxyNamespace('LongitudeOne\Spatial\Tests\Proxies');
        }

        $config->setMetadataDriverImpl(new AttributeDriver($path));

        $this->mockEntityManager = new EntityManager($this->getMockConnection(), $config);

        return $this->mockEntityManager;
    }
}
