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

use Ivory\CKEditorBundle\Exception\PluginManagerException;
use Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper;
use Symfony\Component\Templating\Helper\CoreAssetsHelper;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class PluginManager implements PluginManagerInterface
{
    /** @var \Symfony\Component\Templating\Helper\CoreAssetsHelper */
    protected $assetsHelper;

    /** @var \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper */
    protected $assetsVersionTrimerHelper;

    /** @var array */
    protected $plugins = array();

    /**
     * Creates a plugin manager.
     *
     * @param \Symfony\Component\Templating\Helper\CoreAssetsHelper  $assetsHelper              The assets helper.
     * @param \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper $assetsVersionTrimerHelper The version trimer.
     * @param array                                                  $plugins                   The CKEditor plugins.
     */
    public function __construct(
        CoreAssetsHelper $assetsHelper,
        AssetsVersionTrimerHelper $assetsVersionTrimerHelper,
        array $plugins = array()
    ) {
        $this->setAssetsHelper($assetsHelper);
        $this->setAssetsVersionTrimerHelper($assetsVersionTrimerHelper);
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
     * Gets the assets version trimer helper.
     *
     * @return \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper The assets version trimer helper.
     */
    public function getAssetsVersionTrimerHelper()
    {
        return $this->assetsVersionTrimerHelper;
    }

    /**
     * Sets the assets version trimer helper.
     *
     * @param \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper $assetsVersionTrimerHelper The version trimer.
     */
    public function setAssetsVersionTrimerHelper(AssetsVersionTrimerHelper $assetsVersionTrimerHelper)
    {
        $this->assetsVersionTrimerHelper = $assetsVersionTrimerHelper;
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
        $plugin['path'] = $this->assetsVersionTrimerHelper->trim($this->assetsHelper->getUrl($plugin['path']));

        $this->plugins[$name] = $plugin;
    }
}
