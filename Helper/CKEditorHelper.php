<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Helper;

use Symfony\Component\Templating\Helper\CoreAssetsHelper;
use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Routing\RouterInterface;

/**
 * CKEditor helper.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorHelper extends Helper
{
    /** @var \Symfony\Component\Templating\Helper\CoreAssetsHelper */
    protected $assetsHelper;

    /** @var \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper */
    protected $assetsVersionTrimerHelper;

    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;

    /**
     * Creates a CKEditor template helper.
     *
     * @param \Symfony\Component\Templating\Helper\CoreAssetsHelper  $assetsHelper              The assets helper.
     * @param \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper $assetsVersionTrimerHelper The version trimer.
     * @param \Symfony\Component\Routing\RouterInterface             $router                    The router.
     */
    public function __construct(
        CoreAssetsHelper $assetsHelper,
        AssetsVersionTrimerHelper $assetsVersionTrimerHelper,
        RouterInterface $router
    ) {
        $this->setAssetsHelper($assetsHelper);
        $this->setAssetsVersionTrimerHelper($assetsVersionTrimerHelper);
        $this->setRouter($router);
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
     * Renders the base path.
     *
     * @param string $basePath The base path.
     *
     * @return string The rendered base path.
     */
    public function renderBasePath($basePath)
    {
        return $this->assetsVersionTrimerHelper->trim($this->assetsHelper->getUrl($basePath));
    }

    /**
     * Renders the js path.
     *
     * @param string $jsPath The js path.
     *
     * @return string The rendered js path.
     */
    public function renderJsPath($jsPath)
    {
        return $this->assetsHelper->getUrl($jsPath);
    }

    /**
     * Renders the replace.
     *
     * @param string $id    The identifier.
     * @param array $config The config.
     *
     * @return string The rendered replace.
     */
    public function renderReplace($id, array $config)
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

        $router = $this->router;

        $filebrowser = function ($key, array &$config) use ($router) {
            $filebrowserHandler = 'filebrowser'.$key.'Handler';
            $filebrowserRoute = 'filebrowser'.$key.'Route';
            $filebrowserRouteParameters = 'filebrowser'.$key.'RouteParameters';
            $filebrowserRouteAbsolute = 'filebrowser'.$key.'RouteAbsolute';

            if (isset($config[$filebrowserHandler])) {
                $config['filebrowser'.$key.'Url'] = $config[$filebrowserHandler]($router);
            } elseif (isset($config[$filebrowserRoute])) {
                $config['filebrowser'.$key.'Url'] = $router->generate(
                    $config[$filebrowserRoute],
                    isset($config[$filebrowserRouteParameters]) ? $config[$filebrowserRouteParameters] : array(),
                    isset($config[$filebrowserRouteAbsolute]) ? $config[$filebrowserRouteAbsolute] : false
                );
            }

            unset($config[$filebrowserHandler]);
            unset($config[$filebrowserRoute]);
            unset($config[$filebrowserRouteParameters]);
            unset($config[$filebrowserRouteAbsolute]);
        };

        $keys = array(
            'Browse',
            'FlashBrowse',
            'ImageBrowse',
            'ImageBrowseLink',
            'Upload',
            'FlashUpload',
            'ImageUpload',
        );

        foreach ($keys as $key) {
            $filebrowser($key, $config);
        }

        return sprintf(
            'CKEDITOR.replace("%s", %s);',
            $id,
            preg_replace('/"(CKEDITOR\.[A-Z_]+)"/', '$1', json_encode($config))
        );
    }

    /**
     * Renders the destroy.
     *
     * @param string $id The identifier.
     *
     * @return string The rendered destroy.
     */
    public function renderDestroy($id)
    {
        return <<<EOF
if (CKEDITOR.instances["$id"]) {
    delete CKEDITOR.instances["$id"];
}
EOF;
    }

    /**
     * Renders a plugin.
     *
     * @param string $name   The name.
     * @param array  $plugin The plugin.
     *
     * @return string The rendered plugin.
     */
    public function renderPlugin($name, array $plugin)
    {
        return sprintf(
            'CKEDITOR.plugins.addExternal("%s", "%s", "%s");',
            $name,
            $this->assetsVersionTrimerHelper->trim($this->assetsHelper->getUrl($plugin['path'])),
            $plugin['filename']
        );
    }

    /**
     * Renders a styles set.
     *
     * @param string $name The name
     * @param array $stylesSet The style set.
     *
     * @return The rendered style set.
     */
    public function renderStylesSet($name, array $stylesSet)
    {
        return sprintf('CKEDITOR.stylesSet.add("%s", %s);', $name, json_encode($stylesSet));
    }

    /**
     * Renders a template.
     *
     * @param string $name     The template name.
     * @param array  $template The template.
     *
     * @return string The rendered template.
     */
    public function renderTemplate($name, array $template)
    {
        if (isset($template['imagesPath'])) {
            $template['imagesPath'] = $this->assetsVersionTrimerHelper->trim(
                $this->assetsHelper->getUrl($template['imagesPath'])
            );
        }

        return sprintf('CKEDITOR.addTemplates("%s", %s);', $name, json_encode($template));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ivory_ckeditor';
    }
}
