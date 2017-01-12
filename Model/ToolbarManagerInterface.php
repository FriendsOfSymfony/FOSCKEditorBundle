<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Model;

/**
 * Ivory CKEditor toolbar manager.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ToolbarManagerInterface
{
    /**
     * Checks if the CKEditor items exists.
     *
     * @return boolean TRUE if the CKEditor items exists else FALSE.
     */
    public function hasItems();

    /**
     * Gets the CKEditor items.
     *
     * @return array The CKEditor items.
     */
    public function getItems();

    /**
     * Sets the CKEditor items.
     *
     * @param array $items The CKEditor items.
     *
     * @return null No return value.
     */
    public function setItems(array $items);

    /**
     * Checks if a specific CKEditor item exists.
     *
     * @param string $name The CKEditor item name.
     *
     * @return boolean TRUE if the CKEditor item exists else FALSE.
     */
    public function hasItem($name);

    /**
     * Gets a specific CKEditor item.
     *
     * @param string $name The CKEditor item name.
     *
     * @return array The CKEditor item.
     */
    public function getItem($name);

    /**
     * Sets a CKEditor item.
     *
     * @param string $name    The CKEditor item name.
     * @param array  $item The CKEditor item.
     *
     * @return null No return value.
     */
    public function setItem($name, array $item);
    
    /**
     * Checks if the CKEditor toolbars exists.
     *
     * @return boolean TRUE if the CKEditor toolbars exists else FALSE.
     */
    public function hasToolbars();

    /**
     * Gets the CKEditor toolbars.
     *
     * @return array The CKEditor toolbars.
     */
    public function getToolbars();

    /**
     * Sets the CKEditor toolbars.
     *
     * @param array $toolbars The CKEditor toolbars.
     *
     * @return null No return value.
     */
    public function setToolbars(array $toolbars);

    /**
     * Checks if a specific CKEditor toolbar exists.
     *
     * @param string $name The CKEditor toolbar name.
     *
     * @return boolean TRUE if the CKEditor toolbar exists else FALSE.
     */
    public function hasToolbar($name);

    /**
     * Gets a specific CKEditor toolbar.
     *
     * @param string $name The CKEditor toolbar name.
     *
     * @return array The CKEditor toolbar.
     */
    public function getToolbar($name);

    /**
     * Sets a CKEditor toolbar.
     *
     * @param string $name    The CKEditor toolbar name.
     * @param array  $toolbar The CKEditor toolbar.
     *
     * @return null No return value.
     */
    public function setToolbar($name, array $toolbar);

    /**
     * Resolves a specific CKEditor toolbar.
     *
     * @param string $name The CKEditor toolbar name.
     *
     * @return array The resolved CKEditor toolbar.
     */
    public function resolveToolbar($name);
}
