<?php
/**
 * Configuration file for PHP Coding Standards Fixer (php-cs-fixer).
 *
 * On GitHub: https://github.com/FriendsOfPhp/php-cs-fixer
 * More information: http://cs.sensiolabs.org/
 *
 * @author rugk
 * @copyright Copyright (c) rugk, 2015-2016
 * @license MIT
 */

$finder = Symfony\CS\Finder\DefaultFinder::create()
  ->exclude('vendor')
  ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
  ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
  ->fixers(['concat_with_spaces', 'short_array_syntax', 'standardize_not_equal',
            'phpdoc_params', 'operators_spaces',
            'duplicate_semicolon', 'remove_leading_slash_use', 'align_equals',
            'single_array_no_trailing_comma'])
  ->finder($finder)
;
