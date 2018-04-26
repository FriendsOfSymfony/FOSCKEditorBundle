<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../vendor/autoload.php';

if (!class_exists(TestCase::class)) {
    class_alias(\PHPUnit_Framework_TestCase::class, TestCase::class);
}
