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

$config = new Config();

return $config
    ->setUsingCache(true)
    ->setRules([
        '@Symfony'        => true,
        'array_syntax'    => ['syntax' => 'short'],
        'ordered_imports' => true,
        'yoda_style'      => false,
    ])
    ->setFinder($finder);
