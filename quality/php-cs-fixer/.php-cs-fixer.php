<?php

/**
 * This file is part of the BB-One Project
 *
 * PHP 8.2 | Symfony 6.3.*
 *
 * Copyright LongitudeOne - Alexandre Tranchant
 * Copyright 2023
 *
 */

//Replace the value of this variable with the project's launch year.
$firstYear = 2017;
$loFirstYear = 2020;

function __copyright(int $launchYear): string
{
    $currentYear = (int)date('Y');
    if ($currentYear === $launchYear) {
        return $currentYear;
    }

    return sprintf('%d-%d', $launchYear, $currentYear);
}

$header = file_get_contents(__DIR__ . '/headers.txt');
$header = str_replace("%year%", __copyright($firstYear), $header);
$header = str_replace("%lo-year%", __copyright($loFirstYear), $header);

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/../../lib/',
        __DIR__ . '/../../tests/',
    ]);

$config = new PhpCsFixer\Config();

return $config->setRules([
    '@DoctrineAnnotation' => true,
    '@PhpCsFixer' => true,
    '@PHPUnit100Migration:risky' => true,
    '@PHP84Migration' => true,
    'declare_strict_types' => true,
    'dir_constant' => true, // Remove when @PHPCsFixerRisky is added
    'ereg_to_preg' => true, // Remove when @PHPCsFixerRisky is added
    'header_comment' => [
        'comment_type' => 'PHPDoc',
        'header' => $header,
        'location' => 'after_open',
        'separate' => 'bottom',
    ],
    //        'date_time_immutable' => true,
    'is_null' => true, // Remove when @PHPCsFixerRisky is added
    'mb_str_functions' => true, //Set to false if we only use multibyte string functions for ClassName
    'modernize_types_casting' => true, // Remove when @PHPCsFixerRisky is added
    'no_unneeded_final_method' => true, // Remove when @PHPCsFixerRisky is added
    'ordered_interfaces' => [
        'direction' => 'ascend',
        'order' => 'alpha',
    ],
    'ordered_class_elements' => [
        'order' => [
            'use_trait',
            'constant_public', 'constant_protected', 'constant_private', 'constant',
            'property_public_static', 'property_protected_static', 'property_private_static', 'property_static',
            'property_public', 'property_protected', 'property_private', 'property',
            'construct', 'destruct',
            'phpunit',
            'method_public_static', 'method_protected_static', 'method_private_static', 'method_static',
            'method_public', 'method_protected', 'method_private', 'method', 'magic',
        ],
        'sort_algorithm' => 'alpha',
    ],
    'php_unit_test_case_static_method_calls' => true, //FIXME accepted values are self static or this.
    'single_line_throw' => false, //FIXME set to true when all #60 is fixed
    'single_line_empty_body' => false,
])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/.php_cs.cache');
