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
 * SP_Scale DQL function.
 *
 * Possible PostGIS signatures:
 *  geometry ST_Scale(geometry geom, float xFactor, float yFactor, float zFactor);
 *  geometry ST_Scale(geometry geom, float xFactor, float yFactor);
 *  geometry ST_Scale(geometry geom, geometry factor);
 *  geometry ST_Scale(geometry geom, geometry factor, geometry origin);
 *
 * @see https://postgis.net/docs/ST_Scale.html PostGIS ST_Scale documentation
 *
 * @author  Tom Vogt <tom@lemuria.org>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org
 */
class SpScale extends AbstractSpatialDQLFunction
{
    /**
     * Get the SQL.
     *
     * PostgreSQL cannot choose the numeric ST_Scale overload when scale factors
     * are prepared parameters. Explicitly cast these values to float8.
     * The explicit cast removes this ambiguity and forces the expected
     * ST_Scale(geometry, float, float) signature.
     *
     * @param SqlWalker $sqlWalker the SQL walker
     *
     * @return string SQL declaration of the ST_Scale call
     *
     * @throws ASTException when node cannot dispatch SqlWalker
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        $this->validatePlatform($sqlWalker->getConnection()->getDatabasePlatform());

        $arguments = [];
        foreach ($this->getGeometryExpressions() as $position => $expression) {
            $argument = $expression->dispatch($sqlWalker);
            if (0 !== $position && $expression instanceof InputParameter) {
                $argument = sprintf('CAST(%s AS double precision)', $argument);
            }

            $arguments[] = $argument;
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
        return 'ST_Scale';
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
        return 4;
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
