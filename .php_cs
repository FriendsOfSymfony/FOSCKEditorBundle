<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(__DIR__)
    ->exclude('vendor');

return Config::create()
    ->setUsingCache(true)
    ->setRules([
        '@Symfony'               => true,
        'binary_operator_spaces' => ['align_double_arrow' => true],
        'ordered_imports'        => true,
    ])
    ->setFinder($finder);
