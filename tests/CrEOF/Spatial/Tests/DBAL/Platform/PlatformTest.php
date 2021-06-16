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

namespace CrEOF\Spatial\Tests\DBAL\Platform;

use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\Tests\OrmMockTestCase;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;

/**
 * Spatial platform tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group geometry
 *
 * @internal
 *
 * @covers \CrEOF\Spatial\DBAL\Platform\MySql<extended>
 * @covers \CrEOF\Spatial\DBAL\Platform\PostgreSql<extended>
 */
class PlatformTest extends OrmMockTestCase
{
    /**
     * Setup the test.
     *
     * @throws DBALException When connection failed
     * @throws ORMException  when cache is not set
     */
    public function setUp(): void
    {
        if (!Type::hasType('point')) {
            Type::addType('point', 'CrEOF\Spatial\DBAL\Types\Geometry\PointType');
        }

        parent::setUp();
    }

    /**
     * Test non-supported platform.
     *
     * @throws DBALException  when connection failed
     * @throws ORMException   when cache is not set
     * @throws ToolsException this should not happen
     */
    public function testUnsupportedPlatform()
    {
        $this->expectException(UnsupportedPlatformException::class);
        $this->expectExceptionMessage('DBAL platform "YourSQL" is not currently supported.');

        $metadata = $this->getMockEntityManager()->getClassMetadata('CrEOF\Spatial\Tests\Fixtures\PointEntity');
        $schemaTool = new SchemaTool($this->getMockEntityManager());

        $schemaTool->createSchema([$metadata]);
    }
}
