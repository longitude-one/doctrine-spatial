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

namespace LongitudeOne\Spatial\Tests\DBAL\Types;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\ClassMetadata;
use LongitudeOne\Spatial\Tests\OrmTestCase;

/**
 * Doctrine schema related tests.
 *
 * @group php
 *
 * @internal
 *
 * @covers \LongitudeOne\Spatial\DBAL\Types\AbstractSpatialType
 */
class SchemaTest extends OrmTestCase
{
    /**
     * Set up the geography type test.
     */
    protected function setUp(): void
    {
        $this->usesEntity(self::GEOMETRY_ENTITY);
        $this->usesEntity(self::POINT_ENTITY);
        $this->usesEntity(self::LINESTRING_ENTITY);
        $this->usesEntity(self::POLYGON_ENTITY);
        $this->usesEntity(self::MULTIPOINT_ENTITY);
        $this->usesEntity(self::MULTIPOLYGON_ENTITY);

        // TODO : Verify what MySQL can do with geography
        if ($this->getPlatform() instanceof PostgreSQLPlatform) {
            $this->usesEntity(self::GEOGRAPHY_ENTITY);
            $this->usesEntity(self::GEO_POINT_SRID_ENTITY);
            $this->usesEntity(self::GEO_LINESTRING_ENTITY);
            $this->usesEntity(self::GEO_POLYGON_ENTITY);
        }

        $this->supportsPlatform(MariaDBPlatform::class);
        $this->supportsPlatform(MySQLPlatform::class);
        $this->supportsPlatform(PostgreSQLPlatform::class);
        parent::setUp();
    }

    /**
     * Test doctrine type mapping.
     */
    public function testDoctrineTypeMapping(): void
    {
        $platform = $this->getPlatform();

        foreach ($this->getAllClassMetadata() as $metadata) {
            foreach ($metadata->getFieldNames() as $fieldName) {
                $doctrineType = $metadata->getTypeOfField($fieldName);
                if (null === $doctrineType) {
                    continue;
                }

                try {
                    $type = Type::getType($doctrineType);
                } catch (Exception $e) {
                    static::fail(sprintf('Unable to get doctrine type %s: %s', $doctrineType, $e->getMessage()));
                }
                $databaseTypes = $type->getMappedDatabaseTypes($platform);

                foreach ($databaseTypes as $databaseType) {
                    try {
                        $typeMapping = $this->getPlatform()->getDoctrineTypeMapping($databaseType);
                        static::assertEquals($doctrineType, $typeMapping);
                    } catch (Exception $e) {
                        static::fail(sprintf('Unable to get doctrine type mapping: %s', $e->getMessage()));
                    }
                }
            }
        }
    }

    /**
     * Test to reverse schema mapping.
     */
    public function testSchemaReverseMapping(): void
    {
        $result = $this->getSchemaTool()->getUpdateSchemaSql($this->getAllClassMetadata());

        $message = 'No SQL query should be generated to update schema, but some are generated:';
        foreach ($result as $sql) {
            $message .= sprintf('%s => %s', PHP_EOL, $sql);
        }

        if ($this->getPlatform() instanceof PostgreSQLPlatform) {
            static::markTestSkipped('PostgreSQL known issue:'.$message);
        }

        static::assertCount(0, $result, $message);
    }

    /**
     * All class metadata getter.
     *
     * @return ClassMetadata[]
     */
    private function getAllClassMetadata(): array
    {
        $metadata = [];

        foreach (array_keys($this->getUsedEntityClasses()) as $entityClass) {
            $metadata[] = $this->getEntityManager()->getClassMetadata($entityClass);
        }

        return $metadata;
    }
}
