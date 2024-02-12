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

namespace FOS\CKEditorBundle\Config;

use FOS\CKEditorBundle\Exception\ConfigException;

final class CKEditorConfiguration implements CKEditorConfigurationInterface
{
    /**
     * @var bool
     */
    private $enable;

    /**
     * @var bool
     */
    private $autoload;

    /**
     * @var bool
     */
    private $poweredBy;

    /**
     * @var bool
     */
    private $resize;

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string
     */
    private $jsPath;

    /**
     * @var string|null
     */
    private $defaultConfig;

    /**
     * @var array
     */
    private $configs;

//    /**
//     * @var array
//     */
//    private $template;

    /**
     * @var array
     */
    private $styles;

    /**
     * @var array
     */
    private $plugins;

    public function __construct(array $config)
    {
        if ($config['enable']) {
            $config = $this->resolveConfigs($config);
        }

        $this->enable = $config['enable'];
        $this->autoload = $config['autoload'];
        $this->poweredBy = $config['powered_by'];
        $this->resize = $config['resize'];
        $this->basePath = $config['base_path'];
        $this->jsPath = $config['js_path'];
        $this->defaultConfig = $config['default_config'];
        $this->plugins = $config['plugins'];
        $this->styles = $config['styles'];
        $this->configs = $config['configs'];
    }

    /**
     * @throws ConfigException
     */
    private function resolveConfigs(array $config): array
    {
        if (empty($config['configs'])) {
            return $config;
        }

        if (!isset($config['default_config']) && !empty($config['configs'])) {
            reset($config['configs']);
            $config['default_config'] = key($config['configs']);
        }

        if (isset($config['default_config']) && !isset($config['configs'][$config['default_config']])) {
            throw ConfigException::invalidDefaultConfig($config['default_config']);
        }

        return $config;
    }

    public function getPlugins(): array
    {
        return $this->plugins;
    }

//    public function getTemplate(): array
//    {
//        return $this->template;
//    }

    public function getStyles(): array
    {
        return $this->styles;
    }

    public function isEnable(): bool
    {
        return $this->enable;
    }

    public function isAutoload(): bool
    {
        return $this->autoload;
    }

    public function isPoweredBy(): bool
    {
        return $this->poweredBy;
    }

    public function isResize(): bool
    {
        return $this->resize;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getJsPath(): string
    {
        return $this->jsPath;
    }

    public function getDefaultConfig(): ?string
    {
        return $this->defaultConfig;
    }

    public function getConfigs(): array
    {
        return $this->configs;
    }

    /**
     * @throws ConfigException
     */
    public function getConfig(string $name): array
    {
        if (!isset($this->configs[$name])) {
            throw ConfigException::configDoesNotExist($name);
        }

        return $this->configs[$name];
    }
}
