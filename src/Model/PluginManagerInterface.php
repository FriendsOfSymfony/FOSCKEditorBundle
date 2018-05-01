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
interface PluginManagerInterface
{
    /**
     * @return bool
     */
    public function hasPlugins();

    /**
     * @return array
     */
    public function getPlugins();

    /**
     * @param array $plugins
     */
    public function setPlugins(array $plugins);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasPlugin($name);

    /**
     * @param string $name
     *
     * @return array
     */
    public function getPlugin($name);

    /**
     * @param string $name
     * @param array  $plugin
     */
    public function setPlugin($name, array $plugin);
}
