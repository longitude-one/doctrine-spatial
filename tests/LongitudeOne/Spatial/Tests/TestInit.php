<?php
/**
 * This file is part of the doctrine spatial extension.
 *
 * PHP 8.1
 *
 * Copyright Alexandre Tranchant <alexandre.tranchant@gmail.com> 2017-2024
 * Copyright Longitude One 2020-2024
 * Copyright 2015 Derek J. Lambert
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

use Composer\Autoload\ClassLoader;

require __DIR__.'/../../../../vendor/autoload.php';

error_reporting(E_ALL | E_STRICT);

$loader = new ClassLoader();
$loader->add('LongitudeOne\Spatial\Tests', __DIR__.'/../../..');
$loader->add('Doctrine\Tests', __DIR__.'/../../../../vendor/doctrine/orm/tests');
$loader->register();
