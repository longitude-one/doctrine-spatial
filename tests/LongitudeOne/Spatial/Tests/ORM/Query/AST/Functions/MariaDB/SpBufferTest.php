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

namespace LongitudeOne\Spatial\Tests\ORM\Query\AST\Functions\MariaDB;

use Doctrine\DBAL\Platforms\MariaDBPlatform;
use LongitudeOne\Spatial\Tests\Helper\PersistantPointHelperTrait;
use LongitudeOne\Spatial\Tests\PersistOrmTestCase;

/**
 * ST_Buffer DQL function tests.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org MIT
 *
 * @group dql
 * @group mariadb-only
 *
 * @internal
 *
 * @coversDefaultClass
 */
class SpBufferTest extends PersistOrmTestCase
{
    use PersistantPointHelperTrait;

    /**
     * Set up the function type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POINT_ENTITY);
        $this->supportsPlatform(MariaDBPlatform::class);

        parent::setUp();
    }

    /**
     * Test a DQL containing function to test in the select.
     *
     * @group geometry
     */
    public function testSelectSpBuffer(): void
    {
        $pointO = $this->persistPointO();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT p, ST_AsText(MariaDB_Buffer(p.point, 4)) FROM LongitudeOne\Spatial\Tests\Fixtures\PointEntity p'
        );

        $result = $query->getResult();

        static::assertIsArray($result);
        static::assertCount(1, $result);
        static::assertEquals($pointO, $result[0][0]);
        static::assertEquals('POLYGON((0 -4,-0.1962706973096721 -3.9951818248206896,-0.3920685613182424 -3.9807389066887873,-0.5869218978214472 -3.956706039859124,-0.7803612880645132 -3.9231411216129217,-0.9719207196130556 -3.880125012778176,-1.161138709017849 -3.8277613429288357,-1.3475594135688804 -3.7661762607320832,-1.5307337294603591 -3.695518130045147,-1.7102203737211283 -3.6159571724937734,-1.8855869473039903 -3.5276850573934198,-2.0564109767728866 -3.4309144400010885,-2.2222809320784087 -3.325878449210181,-2.3827972179697334 -3.2128301259225793,-2.537573136654582 -3.092041813450948,-2.6862358193880733 -2.9638045014198364,-2.82842712474619 -2.82842712474619,-2.9638045014198364 -2.6862358193880733,-3.092041813450948 -2.537573136654582,-3.2128301259225793 -2.3827972179697334,-3.325878449210181 -2.2222809320784087,-3.4309144400010885 -2.0564109767728866,-3.5276850573934198 -1.8855869473039903,-3.6159571724937734 -1.7102203737211283,-3.695518130045147 -1.5307337294603591,-3.7661762607320832 -1.3475594135688804,-3.8277613429288357 -1.161138709017849,-3.880125012778176 -0.9719207196130556,-3.9231411216129217 -0.7803612880645132,-3.956706039859124 -0.5869218978214472,-3.9807389066887873 -0.3920685613182424,-3.9951818248206896 -0.1962706973096721,-4 0,-3.9807389066887873 0.3920685613182424,-3.956706039859124 0.5869218978214472,-3.9231411216129217 0.7803612880645132,-3.880125012778176 0.9719207196130556,-3.8277613429288357 1.161138709017849,-3.7661762607320832 1.3475594135688804,-3.695518130045147 1.5307337294603591,-3.6159571724937734 1.7102203737211283,-3.5276850573934198 1.8855869473039903,-3.4309144400010885 2.0564109767728866,-3.325878449210181 2.2222809320784087,-3.2128301259225793 2.3827972179697334,-3.092041813450948 2.537573136654582,-2.9638045014198364 2.6862358193880733,-2.82842712474619 2.82842712474619,-2.6862358193880733 2.9638045014198364,-2.537573136654582 3.092041813450948,-2.3827972179697334 3.2128301259225793,-2.2222809320784087 3.325878449210181,-2.0564109767728866 3.4309144400010885,-1.8855869473039903 3.5276850573934198,-1.7102203737211283 3.6159571724937734,-1.5307337294603591 3.695518130045147,-1.3475594135688804 3.7661762607320832,-1.161138709017849 3.8277613429288357,-0.9719207196130556 3.880125012778176,-0.7803612880645132 3.9231411216129217,-0.5869218978214472 3.956706039859124,-0.3920685613182424 3.9807389066887873,-0.1962706973096721 3.9951818248206896,0 4,0.1962706973096721 3.9951818248206896,0.3920685613182424 3.9807389066887873,0.5869218978214472 3.956706039859124,0.7803612880645132 3.9231411216129217,0.9719207196130556 3.880125012778176,1.161138709017849 3.8277613429288357,1.3475594135688804 3.7661762607320832,1.5307337294603591 3.695518130045147,1.7102203737211283 3.6159571724937734,1.8855869473039903 3.5276850573934198,2.0564109767728866 3.4309144400010885,2.2222809320784087 3.325878449210181,2.3827972179697334 3.2128301259225793,2.537573136654582 3.092041813450948,2.6862358193880733 2.9638045014198364,2.82842712474619 2.82842712474619,2.9638045014198364 2.6862358193880733,3.092041813450948 2.537573136654582,3.2128301259225793 2.3827972179697334,3.325878449210181 2.2222809320784087,3.4309144400010885 2.0564109767728866,3.5276850573934198 1.8855869473039903,3.6159571724937734 1.7102203737211283,3.695518130045147 1.5307337294603591,3.7661762607320832 1.3475594135688804,3.8277613429288357 1.161138709017849,3.880125012778176 0.9719207196130556,3.9231411216129217 0.7803612880645132,3.956706039859124 0.5869218978214472,3.9807389066887873 0.3920685613182424,3.9951818248206896 0.1962706973096721,4 0,3.9807389066887873 -0.3920685613182424,3.956706039859124 -0.5869218978214472,3.9231411216129217 -0.7803612880645132,3.880125012778176 -0.9719207196130556,3.8277613429288357 -1.161138709017849,3.7661762607320832 -1.3475594135688804,3.695518130045147 -1.5307337294603591,3.6159571724937734 -1.7102203737211283,3.5276850573934198 -1.8855869473039903,3.4309144400010885 -2.0564109767728866,3.325878449210181 -2.2222809320784087,3.2128301259225793 -2.3827972179697334,3.092041813450948 -2.537573136654582,2.9638045014198364 -2.6862358193880733,2.82842712474619 -2.82842712474619,2.6862358193880733 -2.9638045014198364,2.537573136654582 -3.092041813450948,2.3827972179697334 -3.2128301259225793,2.2222809320784087 -3.325878449210181,2.0564109767728866 -3.4309144400010885,1.8855869473039903 -3.5276850573934198,1.7102203737211283 -3.6159571724937734,1.5307337294603591 -3.695518130045147,1.3475594135688804 -3.7661762607320832,1.161138709017849 -3.8277613429288357,0.9719207196130556 -3.880125012778176,0.7803612880645132 -3.9231411216129217,0.5869218978214472 -3.956706039859124,0.3920685613182424 -3.9807389066887873,0.1962706973096721 -3.9951818248206896,0 -4))', $result[0][1]);
    }
}