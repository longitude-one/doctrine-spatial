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

namespace CrEOF\Spatial\Tests\ORM\Query;

use CrEOF\Spatial\Exception\InvalidValueException;
use CrEOF\Spatial\Exception\UnsupportedPlatformException;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\Tests\Fixtures\GeometryEntity;
use CrEOF\Spatial\Tests\Helper\PolygonHelperTrait;
use CrEOF\Spatial\Tests\OrmTestCase;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Version;
use Doctrine\ORM\ORMException;

/**
 * DQL type wrapping tests.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * @group dql
 *
 * @internal
 * @coversDefaultClass
 */
class WrappingTest extends OrmTestCase
{
    use PolygonHelperTrait;

    /**
     * Setup the function type test.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->usesEntity(self::GEOMETRY_ENTITY);
        $this->usesType('point');
        parent::setUp();
    }

    //phpcs:disable Squiz.Commenting.FunctionCommentThrowTag.WrongNumber

    /**
     * Test a DQL containing function to test in the predicate.
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *
     * @group geometry
     */
    public function testTypeWrappingSelect()
    {
        $this->createBigPolygon();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $dql = 'SELECT p, ST_Contains(p.polygon, :geometry) FROM CrEOF\Spatial\Tests\Fixtures\PolygonEntity p';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('geometry', new Point(2, 2), 'point');
        $query->processParameterValue('geometry');

        $result = $query->getSQL();

        $parameter = Type::getType('point')->convertToDatabaseValueSql('?', $this->getPlatform());

        $regex = preg_quote(sprintf('/.polygon, %s)/', $parameter));

        static::assertRegExp($regex, $result);
    }

    // phpcs:enable

    /**
     * @group geometry
     *
     * @throws DBALException                when connection failed
     * @throws ORMException                 when cache is not set
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws InvalidValueException        when geometries are not valid
     *     */
    public function testTypeWrappingWhere()
    {
        $entity = new GeometryEntity();

        $entity->setGeometry(new Point(5, 5));
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $query = $this->getEntityManager()->createQuery(
            'SELECT g FROM CrEOF\Spatial\Tests\Fixtures\GeometryEntity g WHERE g.geometry = :geometry'
        );

        $query->setParameter('geometry', new Point(5, 5), 'point');
        $query->processParameterValue('geometry');

        $result = $query->getSQL();
        $parameter = '?';

        if (Version::compare('2.5') <= 0) {
            $parameter = Type::getType('point')->convertToDatabaseValueSql($parameter, $this->getPlatform());
        }

        $regex = preg_quote(sprintf('/geometry = %s/', $parameter));

        static::assertRegExp($regex, $result);
    }
}
