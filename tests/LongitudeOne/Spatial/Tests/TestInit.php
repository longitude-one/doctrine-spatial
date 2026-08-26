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

use Composer\Autoload\ClassLoader;

require __DIR__.'/../../../../vendor/autoload.php';

error_reporting(E_ALL);

$loader = new ClassLoader();
$loader->add('LongitudeOne\Spatial\Tests', __DIR__.'/../../..');
$loader->add('Doctrine\Tests', __DIR__.'/../../../../vendor/doctrine/orm/tests');
$loader->register();
