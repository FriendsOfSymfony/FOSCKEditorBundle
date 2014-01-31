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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * CKEditor helper.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorHelper extends Helper
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    protected $container;

    /**
     * Creates a CKEditor template helper.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container The container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
        return $this->getAssetsVersionTrimerHelper()->trim($this->getAssetsHelper()->getUrl($basePath));
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
        return $this->getAssetsHelper()->getUrl($jsPath);
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
                $config['contentsCss'][] = $this->getAssetsVersionTrimerHelper()->trim(
                    $this->getAssetsHelper()->getUrl($cssContent)
                );
            }
        }

        $router = $this->getRouter();

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
            $this->getAssetsVersionTrimerHelper()->trim($this->getAssetsHelper()->getUrl($plugin['path'])),
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
            $template['imagesPath'] = $this->getAssetsVersionTrimerHelper()->trim(
                $this->getAssetsHelper()->getUrl($template['imagesPath'])
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

    /**
     * Gets the assets helper.
     *
     * @return \Symfony\Component\Templating\Helper\CoreAssetsHelper The assets helper.
     */
    protected function getAssetsHelper()
    {
        return $this->container->get('templating.helper.assets');
    }

    /**
     * Gets the assets version trimer helper.
     *
     * @return \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper The assets version trimer helper.
     */
    protected function getAssetsVersionTrimerHelper()
    {
        return $this->container->get('ivory_ck_editor.helper.assets_version_trimer');
    }

    /**
     * Gets the router.
     *
     * @return \Symfony\Component\Routing\RouterInterface The router.
     */
    protected function getRouter()
    {
        return $this->container->get('router');
    }
}
