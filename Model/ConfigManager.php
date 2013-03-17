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
    Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper,
    Symfony\Component\Routing\RouterInterface,
    Symfony\Component\Templating\Helper\CoreAssetsHelper;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class ConfigManager implements ConfigManagerInterface
{
    /** @var \Symfony\Component\Templating\Helper\CoreAssetsHelper */
    protected $assetsHelper;

    /** @var \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper */
    protected $assetsVersionTrimerHelper;

    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;

    /** @var array */
    protected $configs;

    /**
     * Creates a CKEditor config manager.
     *
     * @param \Symfony\Component\Templating\Helper\CoreAssetsHelper  $assetsHelper              The assets helper.
     * @param \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper $assetsVersionTrimerHelper The assets version trimer helper.
     * @param \Symfony\Component\Routing\RouterInterface             $router                    The router.
     * @param array                                                  $configs                   The CKEditor configs.
     */
    public function __construct(
        CoreAssetsHelper $assetsHelper,
        AssetsVersionTrimerHelper $assetsVersionTrimerHelper,
        RouterInterface $router,
        array $configs = array()
    )
    {
        $this->setAssetsHelper($assetsHelper);
        $this->setAssetsVersionTrimerHelper($assetsVersionTrimerHelper);
        $this->setRouter($router);
        $this->setConfigs($configs);
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
     * @param \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper $assetsVersionTrimerHelper The assets version trimer helper.
     */
    public function setAssetsVersionTrimerHelper(AssetsVersionTrimerHelper $assetsVersionTrimerHelper)
    {
        $this->assetsVersionTrimerHelper = $assetsVersionTrimerHelper;
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
        $config = $this->handleContentsCss($config);
        $config = $this->handleFileBrowser($config);

        $this->configs[$name] = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeConfig($name, array $config)
    {
        $this->setConfig($name, array_merge($this->getConfig($name), $config));
    }

    /**
     * Handles contens css config.
     *
     * @param array $config The config.
     *
     * @return array The handled config.
     */
    protected function handleContentsCss(array $config)
    {
        if (isset($config['contentsCss'])) {
            $cssContents = (array) $config['contentsCss'];

            $config['contentsCss'] = array();
            foreach ($cssContents as $cssContent) {
                $config['contentsCss'][] = $this->assetsVersionTrimerHelper->trim(
                    $this->assetsHelper->getUrl($cssContent)
                );
            }
        }

        return $config;
    }

    /**
     * Handles the file browser config.
     *
     * @param array $config The condig.
     *
     * @return array The handled config.
     */
    protected function handleFileBrowser(array $config)
    {
        $filebrowser = function ($key, array &$config, RouterInterface $router) {
            $filebrowserRoute = 'filebrowser'.$key.'Route';

            if (isset($config[$filebrowserRoute])) {
                $filebrowserRouteParameters = 'filebrowser'.$key.'RouteParameters';
                $filebrowserRouteAbsolute = 'filebrowser'.$key.'RouteAbsolute';

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

        return $config;
    }
}
