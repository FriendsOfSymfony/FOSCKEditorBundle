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

use Ivory\CKEditorBundle\Exception\ConfigManagerException,
    Symfony\Component\Routing\RouterInterface;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConfigManager implements ConfigManagerInterface
{
    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;

    /** @var array */
    protected $configs;

    /**
     * Creates a CKEditor config manager.
     *
     * @param \Symfony\Component\Routing\RouterInterface $router  The router.
     * @param array                                      $configs The CKEditor configs.
     */
    public function __construct(RouterInterface $router, array $configs = array())
    {
        $this->router = $router;
        $this->setConfigs($configs);
    }

    /**
     * Gets the router.
     *
     * @return \Symfony\Component\Routing\RouterInterface The router.
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Sets the router.
     *
     * @param \Symfony\Component\Routing\RouterInterface $router The router.
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
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
        $filebrowser = function ($key, array &$config, RouterInterface $router) {
            $filebrowserRoute = 'filebrowser'.$key.'Route';
            $filebrowserRouteParameters = 'filebrowser'.$key.'RouteParameters';
            $filebrowserRouteAbsolute = 'filebrowser'.$key.'RouteAbsolute';

            if (isset($config[$filebrowserRoute])) {
                $config['filebrowser'.$key.'Url'] = $router->generate(
                    $config[$filebrowserRoute],
                    isset($config[$filebrowserRouteParameters]) ? $config[$filebrowserRouteParameters] : array(),
                    isset($config[$filebrowserRouteAbsolute]) ? $config[$filebrowserRouteAbsolute] : false
                );

                unset($config[$filebrowserRoute]);
                unset($config[$filebrowserRouteParameters]);
                unset($config[$filebrowserRouteAbsolute]);
            }
        };

        $filebrowser('Browse', $config, $this->router);
        $filebrowser('FlashBrowse', $config, $this->router);
        $filebrowser('ImageBrowse', $config, $this->router);
        $filebrowser('ImageBrowseLink', $config, $this->router);

        $filebrowser('Upload', $config, $this->router);
        $filebrowser('FlashUpload', $config, $this->router);
        $filebrowser('ImageUpload', $config, $this->router);

        $this->configs[$name] = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeConfig($name, array $config)
    {
        $this->setConfig($name, array_merge($this->getConfig($name), $config));
    }
}
