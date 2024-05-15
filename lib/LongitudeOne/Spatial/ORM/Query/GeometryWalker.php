<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP          8.1 | 8.2 | 8.3
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

namespace LongitudeOne\Spatial\ORM\Query;

use Doctrine\Common\Lexer\Token;
use Doctrine\ORM\Mapping\AssociationMapping;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\AST\SelectExpression;
use Doctrine\ORM\Query\ParserResult;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;
use LongitudeOne\Spatial\ORM\Query\AST\Functions\ReturnsGeometryInterface;

/**
 * GeometryWalker.
 *
 * Custom DQL AST walker to return geometry objects from queries instead of strings.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 */
class GeometryWalker extends SqlWalker
{
    /**
     * Result set mapping.
     */
    protected ResultSetMapping $resultSetMapping;

    /**
     * Initializes TreeWalker with important information about the ASTs to be walked.
     *
     * @param Query                                                                                                                                                                                                                  $query           the parsed Query
     * @param ParserResult                                                                                                                                                                                                           $parserResult    the result of the parsing process
     * @param array<string, array{metadata?: ClassMetadata<object>, parent?: null|string, relation?: null|AssociationMapping, map?: null|string, resultVariable?: Node|string, nestingLevel: int, token: Token<TokenType, string> }> $queryComponents the query components (symbol table)
     */
    public function __construct($query, $parserResult, array $queryComponents)
    {
        $this->resultSetMapping = $parserResult->getResultSetMapping();

        parent::__construct($query, $parserResult, $queryComponents);
    }

    /**
     * Walks down a SelectExpression AST node and generates the corresponding SQL.
     *
     * @param SelectExpression $selectExpression Select expression AST node
     *
     * @return string the SQL
     *
     * @throws QueryException when error happens during walking into select expression
     *
     * DO NOT ADD SelectExpression TYPEHINT here, because library won't work with ORM ^2.19
     */
    public function walkSelectExpression($selectExpression): string
    {
        $expr = $selectExpression->expression;
        $sql = parent::walkSelectExpression($selectExpression);

        if ($expr instanceof ReturnsGeometryInterface && !$selectExpression->hiddenAliasResultVariable) {
            $alias = mb_strrchr($sql, ' ');
            // Theoretically, $alias cannot be false, but in this case it will be ignored
            if (false !== $alias) {
                $alias = trim($alias);
                $this->resultSetMapping->typeMappings[$alias] = 'geometry';
            }
        }

        return $sql;
    }
}
