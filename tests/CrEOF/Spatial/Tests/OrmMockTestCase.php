<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 7.4 | 8.0
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017 - 2021
 * (c) Longitude One 2020 - 2021
 * (c) 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace CrEOF\Spatial\Tests;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Common test code.
 */
abstract class OrmMockTestCase extends TestCase
{
    /**
     * @var EntityManagerInterface
     */
    protected $mockEntityManager;

    /**
     * Setup the mocked entity manager.
     *
     * @throws DBALException when connection is not successful
     * @throws ORMException  when cache is not set
     */
    protected function setUp(): void
    {
        $this->mockEntityManager = $this->getMockEntityManager();
    }

    /**
     * Return the mocked connection.
     *
     * @throws DBALException when connection is not successful
     *
     * @return Connection
     */
    protected function getMockConnection()
    {
        /** @var Driver|MockObject $driver */
        $driver = $this->getMockBuilder('Doctrine\DBAL\Driver\PDOSqlite\Driver')
            ->setMethods(['getDatabasePlatform'])
            ->getMock()
        ;
        $platform = $this->getMockBuilder('Doctrine\DBAL\Platforms\SqlitePlatform')
            ->setMethods(['getName'])
            ->getMock()
        ;

        $platform->method('getName')
            ->willReturn('YourSQL')
        ;
        $driver->method('getDatabasePlatform')
            ->willReturn($platform)
        ;

        return new Connection([], $driver);
    }

    /**
     * Get the mocked entity manager.
     *
     * @throws DBALException When connection is not successful
     * @throws ORMException  won't happen because Metadata cache is set
     *
     * @return EntityManagerInterface a mocked entity manager
     */
    protected function getMockEntityManager()
    {
        if (isset($this->mockEntityManager)) {
            return $this->mockEntityManager;
        }

        $path = [realpath(__DIR__.'/Fixtures')];
        $config = new Configuration();

        $config->setMetadataCacheImpl(new ArrayCache());
        $config->setProxyDir(__DIR__.'/Proxies');
        $config->setProxyNamespace('CrEOF\Spatial\Tests\Proxies');
        //TODO Warning wrong paramater is provided
        $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver($path, true));

        return EntityManager::create($this->getMockConnection(), $config);
    }
}
