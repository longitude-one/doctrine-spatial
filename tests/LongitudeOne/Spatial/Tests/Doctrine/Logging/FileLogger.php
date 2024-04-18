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

namespace LongitudeOne\Spatial\Tests\Doctrine\Logging;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;

class FileLogger extends MonologLogger implements LoggerInterface
{
    public function __construct(?\DateTimeZone $timezone = null)
    {
        // TODO USE CONSTANT AND GLOBALS
        $name = 'PHPUnit';
        $handler = new StreamHandler(__DIR__.'/../../../../../../.phpunit.cache/sql.log', MonologLogger::DEBUG);
        $processor = new PsrLogMessageProcessor(null, true);
        parent::__construct($name, [$handler], [$processor], $timezone);
    }
}
