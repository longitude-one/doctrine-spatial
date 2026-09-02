<?php
/**
 * This file is part of the Doctrine Spatial extension.
 *
 * PHP 8.4 | 8.5
 * Doctrine ORM ^3.6
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

namespace LongitudeOne\Spatial\ORM\Query\AST\Functions\PostgreSql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\Query\AST\ASTException;
use Doctrine\ORM\Query\AST\InputParameter;
use Doctrine\ORM\Query\SqlWalker;
use LongitudeOne\Spatial\DBAL\Helper\MatchPlatformHelper;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\AbstractSpatialDQLFunction;

/**
 * SP_Transform DQL function.
 *
 * TODO #62 Fix test of this function.
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org
 */
class SpTransform extends AbstractSpatialDQLFunction
{
    /**
     * Get the SQL.
     *
     * PostgreSQL cannot choose between the integer SRID and text PROJ overloads
     * when the target coordinate reference system is a prepared parameter.
     * Numeric parameters are therefore represented as EPSG identifiers, while
     * PROJ strings are passed through unchanged.
     *
     * @param SqlWalker $sqlWalker the SQL walker
     *
     * @return string SQL declaration of the ST_Transform call
     *
     * @throws ASTException when node cannot dispatch SqlWalker
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        $this->validatePlatform($sqlWalker->getConnection()->getDatabasePlatform());

        $expressions = $this->getGeometryExpressions();
        $arguments = [];
        foreach ($expressions as $position => $expression) {
            if (1 === $position && 2 === count($expressions) && $expression instanceof InputParameter) {
                $arguments[] = sprintf(
                    "CASE WHEN CAST(%s AS text) ~ '^[0-9]+$' THEN 'EPSG:' || CAST(%s AS text) ELSE CAST(%s AS text) END",
                    $expression->dispatch($sqlWalker),
                    $expression->dispatch($sqlWalker),
                    $expression->dispatch($sqlWalker),
                );

                continue;
            }

            $arguments[] = $expression->dispatch($sqlWalker);
        }

        $helper = new MatchPlatformHelper();
        $platform = $helper->getSpatialPlatform($sqlWalker->getConnection()->getDatabasePlatform());

        return $platform->getFunctionSqlDeclaration($this->getFunctionName(), $arguments);
    }

    /**
     * Function SQL name getter.
     *
     * @since 2.0 This function replace the protected property functionName.
     */
    protected function getFunctionName(): string
    {
        return 'ST_Transform';
    }

    /**
     * Maximum number of parameter for the spatial function.
     *
     * @since 2.0 This function replace the protected property maxGeomExpr.
     *
     * @return int the inherited methods shall NOT return null, but 0 when function has no parameter
     */
    protected function getMaxParameter(): int
    {
        return 3;
    }

    /**
     * Minimum number of parameter for the spatial function.
     *
     * @since 2.0 This function replace the protected property minGeomExpr.
     *
     * @return int the inherited methods shall NOT return null, but 0 when function has no parameter
     */
    protected function getMinParameter(): int
    {
        return 2;
    }

    /**
     * Get the platforms accepted.
     *
     * @since 2.0 This function replace the protected property platforms.
     * @since 5.0 This function returns the class-string[] instead of string[]
     *
     * @return class-string<AbstractPlatform>[] a non-empty array of accepted platforms
     */
    protected function getPlatforms(): array
    {
        return [PostgreSQLPlatform::class];
    }
}
