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

use FOS\CKEditorBundle\Exception\PluginManagerException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class PluginManager implements PluginManagerInterface
{
    /**
     * @var array
     */
    private $plugins = [];

    /**
     * @param array $plugins
     */
    public function __construct(array $plugins = [])
    {
        $this->setPlugins($plugins);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPlugins()
    {
        return !empty($this->plugins);
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlugins(array $plugins)
    {
        foreach ($plugins as $name => $plugin) {
            $this->setPlugin($name, $plugin);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasPlugin($name)
    {
        return isset($this->plugins[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getPlugin($name)
    {
        if (!$this->hasPlugin($name)) {
            throw PluginManagerException::pluginDoesNotExist($name);
        }

        return $this->plugins[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function setPlugin($name, array $plugin)
    {
        $this->plugins[$name] = $plugin;
    }
}
