<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Exception;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class DependencyInjectionException extends Exception
{
    /**
     * @param string $name
     *
     * @return DependencyInjectionException
     */
    public static function invalidDefaultConfig($name)
    {
        return new static(sprintf('The default config "%s" does not exist.', $name));
    }
}
