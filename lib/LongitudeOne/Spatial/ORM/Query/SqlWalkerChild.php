<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1 | 8.2 | 8.3
 * Doctrine ORM 2.19 | 3.1
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

namespace LongitudeOne\Spatial\ORM\Query;

use Doctrine\ORM\Query\SqlOutputWalker;
use Doctrine\ORM\Query\SqlWalker;

/*
 * GeometryWalker.
 *
 * Custom DQL AST walker to return geometry objects from queries instead of strings.
 *
 * @author  Derek J. Lambert <dlambert@dereklambert.com>
 * @license https://dlambert.mit-license.org MIT
 */
// phpcs:disable Generic.Classes.DuplicateClassName,PSR1.Classes.ClassDeclaration.MultipleClasses
if (class_exists('\Doctrine\ORM\Query\SqlOutputWalker')) {
    abstract class SqlWalkerChild extends SqlOutputWalker
    {
    }
} else {
    abstract class SqlWalkerChild extends SqlWalker
    {
    }
}
// phpcs:enable Generic.Classes.DuplicateClassName,PSR1.Classes.ClassDeclaration.MultipleClasses
