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

namespace LongitudeOne\Spatial\Tests\ORM\Query;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\ORMException;
use LongitudeOne\Spatial\Exception\InvalidValueException;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;
use LongitudeOne\Spatial\PHP\Types\Geometry\Point;
use LongitudeOne\Spatial\Tests\Fixtures\GeometryEntity;
use LongitudeOne\Spatial\Tests\Helper\PolygonHelperTrait;
use LongitudeOne\Spatial\Tests\OrmTestCase;

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
     * @throws Exception                    when connection failed
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
     * @throws Exception                    when connection failed
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

        $dql = 'SELECT p, ST_Contains(p.polygon, :geometry) FROM LongitudeOne\Spatial\Tests\Fixtures\PolygonEntity p';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('geometry', new Point(2, 2), 'point');
        $query->processParameterValue('geometry');

        $result = $query->getSQL();

        $parameter = Type::getType('point')->convertToDatabaseValueSql('?', $this->getPlatform());

        $regex = preg_quote(sprintf('/.polygon, %s)/', $parameter));

        static::assertMatchesRegularExpression($regex, $result);
    }

    // phpcs:enable

    /**
     * @group geometry
     *
     * @throws Exception                    when connection failed
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
            'SELECT g FROM LongitudeOne\Spatial\Tests\Fixtures\GeometryEntity g WHERE g.geometry = :geometry'
        );

        $query->setParameter('geometry', new Point(5, 5), 'point');
        $query->processParameterValue('geometry');

        $result = $query->getSQL();
        $parameter = Type::getType('point')->convertToDatabaseValueSql('?', $this->getPlatform());

        $regex = preg_quote(sprintf('/geometry = %s/', $parameter));

        static::assertMatchesRegularExpression($regex, $result);
    }
}
