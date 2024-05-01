<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
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

namespace LongitudeOne\Spatial\ORM\Query\AST\Functions;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Query\AST\ASTException;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use LongitudeOne\Spatial\Exception\UnsupportedPlatformException;

/**
 * Abstract spatial DQL function.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license https://dlambert.mit-license.org MIT
 *
 * This spatial class is updated to avoid non-covered code. A lot of PostgreSQL functions were not tested,
 * but that was not displayed by coverage rapport. Some MySQL methods generate bug since MySQL 8.0 because their name
 * was updated.
 *
 * It is not possible to evaluate which function is tested or not with a children containing only protected methods.
 * The new pattern consists of create an abstract method for each removed property.
 * Then, if tests don't check function, the code coverage tools will report this information.
 *
 * Thus, if we analyze a platform version, we can implement the getFunctionName method to return geomfromtext for
 * MySQL Version 5.7 and return st_geomfromtext for version 8.0
 *
 * @see https://stackoverflow.com/questions/60377271/why-some-spatial-functions-does-not-exists-on-my-mysql-server
 */
abstract class AbstractSpatialDQLFunction extends FunctionNode
{
    /**
     * @var Node[]
     */
    private $geometryExpression = [];

    /**
     * Get the SQL.
     *
     * @param SqlWalker $sqlWalker the SQL Walker
     *
     * @throws UnsupportedPlatformException when platform is unsupported
     * @throws Exception                    when an invalid platform was specified for this connection
     * @throws ASTException                 when node cannot dispatch SqlWalker
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        $this->validatePlatform($sqlWalker->getConnection()->getDatabasePlatform());

        $arguments = [];
        foreach ($this->getGeometryExpressions() as $expression) {
            $arguments[] = $expression->dispatch($sqlWalker);
        }

        return sprintf('%s(%s)', $this->getFunctionName(), implode(', ', $arguments));
    }

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

        $this->addGeometryExpression($parser->ArithmeticPrimary());

        while (count($this->geometryExpression) < $this->getMinParameter()
            || ((count($this->geometryExpression) < $this->getMaxParameter())
                && TokenType::T_CLOSE_PARENTHESIS != $lexer->lookahead->type)
        ) {
            $parser->match(TokenType::T_COMMA);

            $this->addGeometryExpression($parser->ArithmeticPrimary());
        }

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    /**
     * Geometry expressions fluent adder.
     *
     * @param Node $expression the node expression to add to the array of geometry expression
     *
     * @since 2.0 This function replace the protected property geomExpr which is now private.
     */
    final protected function addGeometryExpression(Node $expression): self
    {
        $this->geometryExpression[] = $expression;

        return $this;
    }

    /**
     * Geometry expressions getter.
     *
     * @since 2.0 This function replace the protected property geomExpr which is now private.
     *
     * @return Node[]
     */
    final protected function getGeometryExpressions(): array
    {
        return $this->geometryExpression;
    }

    /**
     * Check that the current platform supports current spatial function.
     *
     * TODO when support for 8.1 will be dropped, this method will only return true.
     *
     * @param AbstractPlatform $platform database spatial
     *
     * @return true if the current platform is supported
     *
     * @throws UnsupportedPlatformException when platform is unsupported
     */
    protected function validatePlatform(AbstractPlatform $platform): bool
    {
        foreach ($this->getPlatforms() as $acceptedPlatform) {
            if ($platform instanceof $acceptedPlatform) {
                return true;
            }
        }

        throw new UnsupportedPlatformException(
            sprintf('DBAL platform "%s" is not currently supported.', $platform::class)
        );
    }

    /**
     * Function SQL name getter.
     *
     * @since 2.0 This function replace the protected property functionName.
     */
    abstract protected function getFunctionName(): string;

    /**
     * Maximum number of parameters for the spatial function.
     *
     * @since 2.0 This function replace the protected property maxGeomExpr.
     *
     * @return int the inherited methods shall NOT return a null, but 0 when the function has no parameter
     */
    abstract protected function getMaxParameter(): int;

    /**
     * Minimum number of parameters for the spatial function.
     *
     * @since 2.0 This function replace the protected property minGeomExpr.
     *
     * @return int the inherited methods shall NOT return a null, but 0 when the function has no parameter
     */
    abstract protected function getMinParameter(): int;

    /**
     * Get the platforms accepted.
     *
     * The AbstractPlatform::getName() method is now deprecated in the doctrine/dbal component.
     * We now use the class name to identify the platform.
     *
     * @see https://github.com/doctrine/dbal/issues/4749
     * @see https://github.com/longitude-one/doctrine-spatial/issues/40
     *
     * @return class-string[] a non-empty array of accepted platforms
     */
    abstract protected function getPlatforms(): array;
}
