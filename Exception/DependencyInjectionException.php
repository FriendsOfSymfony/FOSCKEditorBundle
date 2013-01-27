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
 * Depencency injection exception.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class DependencyInjectionException extends Exception
{
    /**
     * Gets the "INVALID TOOLBAR ITEM" exception.
     *
     * @param string $item The invalid toolbar item.
     *
     * @return \Ivory\CKEditorBundle\Exception\DependencyInjectionException The "INVALID TOOLBAR ITEM" exception.
     */
    static public function invalidToolbarItem($item)
    {
        return new static(sprintf('The toolbar item "%s" does not exist.', $item));
    }

    /**
     * Getsthe "INVALID TOOLBAR" exception.
     *
     * @param string $toolbar The invalid toolbar.
     *
     * @return \Ivory\CKEditorBundle\Exception\DependencyInjectionException The "INVALID TOOLBAR" exception.
     */
    static public function invalidToolbar($toolbar)
    {
        return new static(sprintf('The toolbar "%s" does not exist.', $toolbar));
    }
}
