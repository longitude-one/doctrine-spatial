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

namespace LongitudeOne\Spatial\ORM\Query\AST\Functions\PostgreSql;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\TokenType;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\AbstractSpatialDQLFunction;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\ReturnsGeometryInterface;

/**
 * ST_SnapToGrid DQL function.
 *
 * @see https://postgis.net/docs/ST_SnapToGrid.html
 *
 * Possible signatures with 2, 3, 5 or 6 parameters:
 *  geometry ST_SnapToGrid(geometry geomA, float size);
 *  geometry ST_SnapToGrid(geometry geomA, float sizeX, float sizeY);
 *  geometry ST_SnapToGrid(geometry geomA, float originX, float originY, float sizeX, float sizeY);
 *  geometry ST_SnapToGrid(geometry geomA, geometry pointOrigin, float sizeX, float sizeY, float sizeZ, float sizeM);
 *
 * @author  Dragos Protung
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://alexandre-tranchant.mit-license.org
 */
class SpSnapToGrid extends AbstractSpatialDQLFunction implements ReturnsGeometryInterface
{
    /**
     * Parse SQL.
     *
     * @param Parser $parser parser
     *
     * @throws QueryException Query exception
     */
    public function parse(Parser $parser): void
    {
        $lexer = $parser->getLexer();

        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        // 1st signature
        $this->addGeometryExpression($parser->ArithmeticFactor());
        $parser->match(TokenType::T_COMMA);
        $this->addGeometryExpression($parser->ArithmeticFactor());

        // 2nd signature
        if (TokenType::T_COMMA === $lexer->lookahead?->type) {
            $parser->match(TokenType::T_COMMA);
            $this->addGeometryExpression($parser->ArithmeticFactor());
        }

        // 3rd signature
        if (TokenType::T_COMMA === $lexer->lookahead?->type) {
            $parser->match(TokenType::T_COMMA);
            $this->addGeometryExpression($parser->ArithmeticFactor());

            $parser->match(TokenType::T_COMMA);
            $this->addGeometryExpression($parser->ArithmeticFactor());

            // 4th signature
            if (TokenType::T_COMMA === $lexer->lookahead->type) {
                // sizeM
                $parser->match(TokenType::T_COMMA);
                $this->addGeometryExpression($parser->ArithmeticFactor());
            }
        }

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    /**
     * Function SQL name getter.
     *
     * @since 2.0 This function replace the protected property functionName.
     */
    protected function getFunctionName(): string
    {
        return 'ST_SnapToGrid';
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
        return 6;
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
