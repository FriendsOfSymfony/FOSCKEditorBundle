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
class ToolbarManagerException extends Exception
{
    /**
     * @param string $name
     *
     * @return ToolbarManagerException
     */
    public static function itemDoesNotExist($name)
    {
        return new static(sprintf('The CKEditor toolbar item "%s" does not exist.', $name));
    }

    /**
     * @param string $name
     *
     * @return ToolbarManagerException
     */
    public static function toolbarDoesNotExist($name)
    {
        return new static(sprintf('The CKEditor toolbar "%s" does not exist.', $name));
    }
}
