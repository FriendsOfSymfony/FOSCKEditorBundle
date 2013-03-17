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

use Ivory\CKEditorBundle\Exception\PluginManagerException,
    Symfony\Component\Templating\Helper\CoreAssetsHelper;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PluginManager implements PluginManagerInterface
{
    /** @var \Symfony\Component\Templating\Helper\CoreAssetsHelper */
    protected $assetsHelper;

    /** @var array */
    protected $plugins;

    /**
     * Creates a plugin manager.
     *
     * @param \Symfony\Component\Templating\Helper\CoreAssetsHelper $assetsHelper The assets helper.
     * @param array                                                 $plugins      The CKEditor plugins.
     */
    public function __construct(CoreAssetsHelper $assetsHelper, array $plugins = array())
    {
        $this->assetsHelper = $assetsHelper;
        $this->setPlugins($plugins);
    }

    /**
     * Gets the assets helper.
     *
     * @return \Symfony\Component\Templating\Helper\CoreAssetsHelper The assets helper.
     */
    public function getAssetsHelper()
    {
        return $this->assetsHelper;
    }

    /**
     * Sets the assets helper.
     *
     * @param \Symfony\Component\Templating\Helper\CoreAssetsHelper $assetsHelper The assets helper.
     */
    public function setAssetsHelper(CoreAssetsHelper $assetsHelper)
    {
        $this->assetsHelper = $assetsHelper;
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
        $plugin['path'] = $this->assetsHelper->getUrl($plugin['path']);

        $this->plugins[$name] = $plugin;
    }
}
