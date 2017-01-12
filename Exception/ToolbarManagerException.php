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
 * Toolbar manager exception.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ToolbarManagerException extends Exception
{
    /**
     * Gets the "ITEM DOES NOT EXIST" exception.
     *
     * @param string $name The invalid CKEditor item name.
     *
     * @return \Ivory\CKEditorBundle\Exception\ToolbarManagerException The "ITEM DOES NOT EXIST" exception.
     */
    public static function itemDoesNotExist($name)
    {
        return new static(sprintf('The CKEditor toolbar item "%s" does not exist.', $name));
    }

    /**
     * Gets the "TOOLBAR DOES NOT EXIST" exception.
     *
     * @param string $name The invalid CKEditor toolbar name.
     *
     * @return \Ivory\CKEditorBundle\Exception\ToolbarManagerException The "TOOLBAR DOES NOT EXIST" exception.
     */
    public static function toolbarDoesNotExist($name)
    {
        return new static(sprintf('The CKEditor toolbar "%s" does not exist.', $name));
    }
}
