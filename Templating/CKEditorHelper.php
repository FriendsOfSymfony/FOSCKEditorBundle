<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Templating;

use Ivory\JsonBuilder\JsonBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * CKEditor helper.
 *
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
class CKEditorHelper extends Helper
{
    /** @var \Ivory\JsonBuilder\JsonBuilder */
    private $jsonBuilder;

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    /**
     * Creates a CKEditor template helper.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container The container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->jsonBuilder = new JsonBuilder();
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
        return $this->fixPath($this->getAssetsHelper()->getUrl($basePath));
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
     * Renders the widget.
     *
     * @param string $id      The identifier.
     * @param array  $config  The config.
     * @param array  $options The options.
     *
     * The available options are:
     *  - auto_inline: boolean
     *  - inline: boolean
     *  - input_sync: boolean
     *
     * @return string The rendered widget.
     */
    public function renderWidget($id, array $config, array $options = array())
    {
        $config = $this->fixConfigLanguage($config);
        $config = $this->fixConfigContentsCss($config);
        $config = $this->fixConfigFilebrowsers($config);

        $this->jsonBuilder
            ->reset()
            ->setValues($config);

        $this->fixConfigEscapedValues($config);

        $autoInline = isset($options['auto_inline']) && !$options['auto_inline']
            ? 'CKEDITOR.disableAutoInline = true;'.PHP_EOL
            : null;

        $widget = sprintf(
            'CKEDITOR.%s("%s", %s);',
            isset($options['inline']) && $options['inline'] ? 'inline' : 'replace',
            $id,
            $this->fixConfigConstants($this->jsonBuilder->build())
        );

        if (isset($options['input_sync']) && $options['input_sync']) {
            $variable = 'ivory_ckeditor_'.$id;
            $widget = 'var '.$variable.' = '.$widget.PHP_EOL;

            return $autoInline.$widget.$variable.'.on(\'change\', function() { '.$variable.'.updateElement(); });';
        }

        return $autoInline.$widget;
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
        return sprintf('if (CKEDITOR.instances["%s"]) { delete CKEDITOR.instances["%s"]; }', $id, $id);
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
            $this->fixPath($this->getAssetsHelper()->getUrl($plugin['path'])),
            $plugin['filename']
        );
    }

    /**
     * Renders a styles set.
     *
     * @param string $name      The name
     * @param array  $stylesSet The style set.
     *
     * @return string The rendered style set.
     */
    public function renderStylesSet($name, array $stylesSet)
    {
        $this->jsonBuilder
            ->reset()
            ->setValues($stylesSet);

        return sprintf(
            'if (CKEDITOR.stylesSet.get("%s") === null) { CKEDITOR.stylesSet.add("%s", %s); }',
            $name,
            $name,
            $this->jsonBuilder->build()
        );
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
            $template['imagesPath'] = $this->fixPath(
                $this->getAssetsHelper()->getUrl($template['imagesPath'])
            );
        }

        $this->jsonBuilder
            ->reset()
            ->setValues($template);

        return sprintf('CKEDITOR.addTemplates("%s", %s);', $name, $this->jsonBuilder->build());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ivory_ckeditor';
    }

    /**
     * Fixes the config language.
     *
     * @param array $config The config.
     *
     * @return array The fixed config.
     */
    protected function fixConfigLanguage(array $config)
    {
        if (isset($config['language'])) {
            $config['language'] = strtolower(str_replace('_', '-', $config['language']));
        }

        return $config;
    }

    /**
     * Fixes the config contents css.
     *
     * @param array $config The config.
     *
     * @return array The fixed config.
     */
    private function fixConfigContentsCss(array $config)
    {
        if (isset($config['contentsCss'])) {
            $cssContents = (array) $config['contentsCss'];

            $config['contentsCss'] = array();
            foreach ($cssContents as $cssContent) {
                $config['contentsCss'][] = $this->fixPath($this->getAssetsHelper()->getUrl($cssContent));
            }
        }

        return $config;
    }

    /**
     * Fixes the config filebrowsers.
     *
     * @param array $config The config.
     *
     * @return array The fixed config.
     */
    private function fixConfigFilebrowsers(array $config)
    {
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
            $fileBrowserKey = 'filebrowser'.$key;
            $handler = $fileBrowserKey.'Handler';
            $url = $fileBrowserKey.'Url';
            $route = $fileBrowserKey.'Route';
            $routeParameters = $fileBrowserKey.'RouteParameters';
            $routeAbsolute = $fileBrowserKey.'RouteAbsolute';

            if (isset($config[$handler])) {
                $config[$url] = $config[$handler]($this->getRouter());
            } elseif (isset($config[$route])) {
                $config[$url] = $this->getRouter()->generate(
                    $config[$route],
                    isset($config[$routeParameters]) ? $config[$routeParameters] : array(),
                    isset($config[$routeAbsolute]) ? $config[$routeAbsolute] : false
                );
            }

            unset($config[$handler]);
            unset($config[$route]);
            unset($config[$routeParameters]);
            unset($config[$routeAbsolute]);
        }

        return $config;
    }

    /**
     * Fixes the config escaped values and sets them on the json builder.
     *
     * @param array $config The config.
     */
    private function fixConfigEscapedValues(array $config)
    {
        if (isset($config['protectedSource'])) {
            foreach ($config['protectedSource'] as $key => $value) {
                $this->jsonBuilder->setValue(sprintf('[protectedSource][%s]', $key), $value, false);
            }
        }

        $escapedValueKeys = array(
            'stylesheetParser_skipSelectors',
            'stylesheetParser_validSelectors',
        );

        foreach ($escapedValueKeys as $escapedValueKey) {
            if (isset($config[$escapedValueKey])) {
                $this->jsonBuilder->setValue(sprintf('[%s]', $escapedValueKey), $config[$escapedValueKey], false);
            }
        }
    }

    /**
     * Fixes the config constants.
     *
     * @param string $json The json config.
     *
     * @return string The fixed config.
     */
    private function fixConfigConstants($json)
    {
        return preg_replace('/"(CKEDITOR\.[A-Z_]+)"/', '$1', $json);
    }

    /**
     * Fixes a path.
     *
     * @param string $path The path.
     *
     * @return string The fixed path.
     */
    private function fixPath($path)
    {
        if (($position = strpos($path, '?')) !== false) {
            return substr($path, 0, $position);
        }

        return $path;
    }

    /**
     * Gets the assets helper.
     *
     * @return \Symfony\Component\Asset\Packages|\Symfony\Component\Templating\Helper\CoreAssetsHelper The assets helper.
     */
    private function getAssetsHelper()
    {
        return $this->container->get('assets.packages');
    }

    /**
     * Gets the router.
     *
     * @return \Symfony\Component\Routing\RouterInterface The router.
     */
    private function getRouter()
    {
        return $this->container->get('router');
    }
}
