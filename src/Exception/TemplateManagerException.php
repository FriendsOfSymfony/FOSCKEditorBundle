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
class TemplateManagerException extends Exception
{
    /**
     * @param string $name
     *
     * @return TemplateManagerException
     */
    public static function templateDoesNotExist($name)
    {
        return new static(sprintf('The CKEditor template "%s" does not exist.', $name));
    }
}
