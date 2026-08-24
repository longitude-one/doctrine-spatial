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

namespace LongitudeOne\Spatial\Tests\Doctrine\Logging;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonologLogger;
use Monolog\Processor\PsrLogMessageProcessor;
use Psr\Log\LoggerInterface;

class FileLogger extends MonologLogger implements LoggerInterface
{
    /**
     * FileLogger constructor.
     */
    public function __construct()
    {
        $logParams = static::getCommonLogParams();
        $filename = self::getFinalFilename($logParams['directory'], $logParams['filename']);
        $name = 'PHPUnit';
        $level = MonologLogger::toMonologLevel($logParams['level']);
        $handler = new StreamHandler($filename, $level);
        $processor = new PsrLogMessageProcessor(null, true);
        parent::__construct($name, [$handler], [$processor], $logParams['timezone']);
    }

    /**
     * Return common log parameters.
     *
     * @return array{filename: string, level: ('ALERT'|'alert'|'CRITICAL'|'critical'|'DEBUG'|'debug'|'EMERGENCY'|'emergency'|'ERROR'|'error'|'INFO'|'info'|'NOTICE'|'notice'|'WARNING'|'warning'|100|200|250|300|400|500|550|600), directory: string, timezone: \DateTimeZone}
     *
     * @throws \InvalidArgumentException if the timezone is not valid
     */
    protected static function getCommonLogParams(): array
    {
        $logParams = [
            'filename' => 'doctrine-spatial.log',
            'level' => 'debug', // TODO use level instead of mark
            'directory' => '.phpunit.cache/logs',
            'timezone' => new \DateTimeZone('UTC'),
        ];

        if (isset($GLOBALS['opt_log_file'])) {
            if (!is_string($GLOBALS['opt_log_file'])) {
                throw new \InvalidArgumentException('The opt_log_file parameter must be a string.');
            }

            $logParams['filename'] = self::removeFirstAndLastSlash($GLOBALS['opt_log_file']);
        }

        if (isset($GLOBALS['opt_log_level'])) {
            if (!is_string($GLOBALS['opt_log_level'])) {
                throw new \InvalidArgumentException('The opt_log_level parameter must be a string.');
            }

            $logParams['level'] = match (mb_trim(mb_strtolower($GLOBALS['opt_log_level']))) {
                'debug' => 'debug',
                'info' => 'info',
                'notice' => 'notice',
                'warning' => 'warning',
                'error' => 'error',
                'critical' => 'critical',
                'alert' => 'alert',
                'emergency' => 'emergency',
                '100' => 100,
                '200' => 200,
                '250' => 250,
                '300' => 300,
                '400' => 400,
                '500' => 500,
                '550' => 550,
                '600' => 600,
                default => throw new \InvalidArgumentException('The opt_log_level parameter must be one of the following values: debug, info, notice, warning, error, critical, alert, emergency.'),
            };
        }

        if (isset($GLOBALS['opt_log_dir'])) {
            if (!is_string($GLOBALS['opt_log_dir'])) {
                throw new \InvalidArgumentException('The opt_log_dir parameter must be a string.');
            }
            $logParams['directory'] = $GLOBALS['opt_log_dir'];
        }

        if (isset($GLOBALS['opt_log_timezone'])) {
            if (!is_string($GLOBALS['opt_log_timezone'])) {
                throw new \InvalidArgumentException('The opt_log_timezone parameter must be a string.');
            }

            try {
                $logParams['timezone'] = new \DateTimeZone($GLOBALS['opt_log_timezone']);
            } catch (\Exception $e) {
                $message = sprintf(
                    'Unable to create DateTimeZone, fix the `opt_log_message` parameter in your phpunit.xml file: %s',
                    $e->getMessage()
                );

                throw new \InvalidArgumentException($message, $e->getCode(), $e);
            }
        }

        return $logParams;
    }

    /**
     * Return the final filename from the directory project.
     *
     * @param string $directory the directory where the log file is stored
     * @param string $filename  the filename
     *
     * @return string the final filename
     */
    private static function getFinalFilename(string $directory, string $filename): string
    {
        return dirname(__DIR__, 6).DIRECTORY_SEPARATOR.$directory.DIRECTORY_SEPARATOR.$filename;
    }

    /**
     * Remove first and last slash from the chain.
     *
     * @param string $chain the chain to clean
     *
     * @return string the chain without first and last slash
     */
    private static function removeFirstAndLastSlash(string $chain): string
    {
        if ('/' === $chain[0]) {
            $chain = mb_substr($chain, 1);
        }
        if ('/' === $chain[mb_strlen($chain) - 1]) {
            $chain = mb_substr($chain, 0, -1);
        }

        return $chain;
    }
}
