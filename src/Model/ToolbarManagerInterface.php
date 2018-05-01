<?php

/*
 * This file is part of the FOSCKEditor Bundle.
 *
 * (c) 2018 - present  Friends of Symfony
 * (c) 2009 - 2017     Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\CKEditorBundle\Model;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface ToolbarManagerInterface
{
    /**
     * @return bool
     */
    public function hasItems();

    /**
     * @return array
     */
    public function getItems();

    /**
     * @param array $items
     */
    public function setItems(array $items);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasItem($name);

    /**
     * @param string $name
     *
     * @return array
     */
    public function getItem($name);

    /**
     * @param string $name
     * @param array  $item
     */
    public function setItem($name, array $item);

    /**
     * @return bool
     */
    public function hasToolbars();

    /**
     * @return array
     */
    public function getToolbars();

    /**
     * @param array $toolbars
     */
    public function setToolbars(array $toolbars);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasToolbar($name);

    /**
     * @param string $name
     *
     * @return array
     */
    public function getToolbar($name);

    /**
     * @param string $name
     * @param array  $toolbar
     */
    public function setToolbar($name, array $toolbar);

    /**
     * @param string $name
     *
     * @return array
     */
    public function resolveToolbar($name);
}
