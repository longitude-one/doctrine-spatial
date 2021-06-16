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

namespace CrEOF\Spatial\Tests\ORM\Query\AST\Functions\Standard;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\Tests\Helper\PointHelperTrait;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\ORMException;

/**
 * ST_Buffer DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @coversDefaultClass
 */
class StBufferTest extends OrmTestCase
{
    use PointHelperTrait;

    /**
     * Setup the function type test.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform('postgresql');

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testSelectStBuffer()
    {
        $pointO = $this->createPointO();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            // phpcs:disable Generic.Files.LineLength.MaxExceeded
            'SELECT p, ST_AsText(ST_Buffer(p.point, 4, :p)) FROM CrEOF\Spatial\Tests\Fixtures\PointEntity p'
            // phpcs:enable
        );

        $query->setParameter('p', 'quad_segs=4', 'string');

        $result = $query->getResult();

        static::assertCount(1, $result);
        static::assertEquals($pointO, $result[0][0]);
        // phpcs:disable Generic.Files.LineLength.MaxExceeded
        static::assertEquals('POLYGON((4 0,3.69551813004515 -1.53073372946036,2.82842712474619 -2.82842712474619,1.53073372946036 -3.69551813004515,0 -4,-1.53073372946035 -3.69551813004515,-2.82842712474618 -2.8284271247462,-3.69551813004514 -1.53073372946037,-4 0,-3.69551813004515 1.53073372946035,-2.8284271247462 2.82842712474618,-1.53073372946037 3.69551813004514,0 4,1.53073372946034 3.69551813004515,2.82842712474617 2.82842712474621,3.69551813004514 1.53073372946038,4 0))', $result[0][1]);
        // phpcs:enable
    }
}
