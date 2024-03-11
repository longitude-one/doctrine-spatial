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

namespace LongitudeOne\Spatial\Tests\DBAL\Platform;

use DG\BypassFinals;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use LongitudeOne\Spatial\DBAL\Types\Geometry\PointType;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;
use LongitudeOne\Spatial\Tests\OrmMockTestCase;

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
 * @covers \LongitudeOne\Spatial\DBAL\Platform\MySql<extended>
 * @covers \LongitudeOne\Spatial\DBAL\Platform\PostgreSql<extended>
 */
class PlatformTest extends OrmMockTestCase
{
    /**
     * Setup the test.
     *
     * @throws Exception    When connection failed
     * @throws ORMException when cache is not set
     */
    public function setUp(): void
    {
        BypassFinals::enable();

        if (!Type::hasType('point')) {
            Type::addType('point', PointType::class);
        }

        parent::setUp();
    }

    /**
     * Test non-supported platform.
     *
     * @throws Exception      when connection failed
     * @throws ORMException   when cache is not set
     * @throws ToolsException this should not happen
     */
    public function testUnsupportedPlatform()
    {
        $this->expectException(UnsupportedPlatformException::class);
        $this->expectExceptionMessage('DBAL platform "YourSQL" is not currently supported.');

        $metadata = $this->getMockEntityManager()->getClassMetadata('LongitudeOne\Spatial\Tests\Fixtures\PointEntity');
        $schemaTool = new SchemaTool($this->getMockEntityManager());

        $schemaTool->createSchema([$metadata]);
    }
}
