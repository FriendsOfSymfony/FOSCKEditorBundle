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

use FOS\CKEditorBundle\Exception\ConfigManagerException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConfigManager implements ConfigManagerInterface
{
    /**
     * @var string
     */
    private $defaultConfig;

    /**
     * @var array
     */
    private $configs = [];

    /**
     * @param array       $configs
     * @param string|null $defaultConfig
     */
    public function __construct(array $configs = [], $defaultConfig = null)
    {
        $this->setConfigs($configs);

        if (null !== $defaultConfig) {
            $this->setDefaultConfig($defaultConfig);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultConfig()
    {
        return $this->defaultConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultConfig($defaultConfig)
    {
        if (!$this->hasConfig($defaultConfig)) {
            throw ConfigManagerException::configDoesNotExist($defaultConfig);
        }

        $this->defaultConfig = $defaultConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function hasConfigs()
    {
        return !empty($this->configs);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigs(array $configs)
    {
        foreach ($configs as $name => $config) {
            $this->setConfig($name, $config);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasConfig($name)
    {
        return isset($this->configs[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($name)
    {
        if (!$this->hasConfig($name)) {
            throw ConfigManagerException::configDoesNotExist($name);
        }

        return $this->configs[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig($name, array $config)
    {
        $this->configs[$name] = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeConfig($name, array $config)
    {
        $this->configs[$name] = array_merge($this->getConfig($name), $config);
    }
}
