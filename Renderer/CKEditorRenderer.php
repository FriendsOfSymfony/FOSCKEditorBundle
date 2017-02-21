<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Renderer;

use Ivory\JsonBuilder\JsonBuilder;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorRenderer implements CKEditorRendererInterface
{
    /**
     * @var JsonBuilder
     */
    private $jsonBuilder;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->jsonBuilder = new JsonBuilder();
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function renderBasePath($basePath)
    {
        return $this->fixPath($this->fixUrl($basePath));
    }

    /**
     * {@inheritdoc}
     */
    public function renderJsPath($jsPath)
    {
        return $this->fixUrl($jsPath);
    }

    /**
     * {@inheritdoc}
     */
    public function renderWidget($id, array $config, array $options = [])
    {
        $config = $this->fixConfigLanguage($config);
        $config = $this->fixConfigContentsCss($config);
        $config = $this->fixConfigFilebrowsers(
            $config,
            isset($options['filebrowsers']) ? $options['filebrowsers'] : []
        );

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
     * {@inheritdoc}
     */
    public function renderDestroy($id)
    {
        return sprintf(
            'if (CKEDITOR.instances["%1$s"]) { '.
            'CKEDITOR.instances["%1$s"].destroy(true); '.
            'delete CKEDITOR.instances["%1$s"]; '.
            '}',
            $id
        );
    }

    /**
     * {@inheritdoc}
     */
    public function renderPlugin($name, array $plugin)
    {
        return sprintf(
            'CKEDITOR.plugins.addExternal("%s", "%s", "%s");',
            $name,
            $this->fixPath($this->fixUrl($plugin['path'])),
            $plugin['filename']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function renderStylesSet($name, array $stylesSet)
    {
        $this->jsonBuilder
            ->reset()
            ->setValues($stylesSet);

        return sprintf(
            'if (CKEDITOR.stylesSet.get("%1$s") === null) { '.
            'CKEDITOR.stylesSet.add("%1$s", %2$s); '.
            '}',
            $name,
            $this->jsonBuilder->build()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function renderTemplate($name, array $template)
    {
        if (isset($template['imagesPath'])) {
            $template['imagesPath'] = $this->fixPath($this->fixUrl($template['imagesPath']));
        }

        if (isset($template['templates'])) {
            foreach ($template['templates'] as &$rawTemplate) {
                if (isset($rawTemplate['template'])) {
                    $rawTemplate['html'] = $this->getTemplating()->render(
                        $rawTemplate['template'],
                        isset($rawTemplate['template_parameters']) ? $rawTemplate['template_parameters'] : []
                    );
                }

                unset($rawTemplate['template']);
                unset($rawTemplate['template_parameters']);
            }
        }

        $this->jsonBuilder
            ->reset()
            ->setValues($template);

        return sprintf('CKEDITOR.addTemplates("%s", %s);', $name, $this->jsonBuilder->build());
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function fixConfigLanguage(array $config)
    {
        if (!isset($config['language']) && ($language = $this->getLanguage()) !== null) {
            $config['language'] = $language;
        }

        if (isset($config['language'])) {
            $config['language'] = strtolower(str_replace('_', '-', $config['language']));
        }

        return $config;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function fixConfigContentsCss(array $config)
    {
        if (isset($config['contentsCss'])) {
            $cssContents = (array) $config['contentsCss'];

            $config['contentsCss'] = [];
            foreach ($cssContents as $cssContent) {
                $config['contentsCss'][] = $this->fixPath($this->fixUrl($cssContent));
            }
        }

        return $config;
    }

    /**
     * @param array $config
     * @param array $filebrowsers
     *
     * @return array
     */
    private function fixConfigFilebrowsers(array $config, array $filebrowsers)
    {
        $filebrowsers = array_unique(array_merge([
            'Browse',
            'FlashBrowse',
            'ImageBrowse',
            'ImageBrowseLink',
            'Upload',
            'FlashUpload',
            'ImageUpload',
        ], $filebrowsers));

        foreach ($filebrowsers as $filebrowser) {
            $fileBrowserKey = 'filebrowser'.$filebrowser;
            $handler = $fileBrowserKey.'Handler';
            $url = $fileBrowserKey.'Url';
            $route = $fileBrowserKey.'Route';
            $routeParameters = $fileBrowserKey.'RouteParameters';
            $routeType = $fileBrowserKey.'RouteType';

            if (isset($config[$handler])) {
                $config[$url] = $config[$handler]($this->getRouter());
            } elseif (isset($config[$route])) {
                $config[$url] = $this->getRouter()->generate(
                    $config[$route],
                    isset($config[$routeParameters]) ? $config[$routeParameters] : [],
                    isset($config[$routeType]) ? $config[$routeType] : UrlGeneratorInterface::ABSOLUTE_PATH
                );
            }

            unset($config[$handler]);
            unset($config[$route]);
            unset($config[$routeParameters]);
            unset($config[$routeType]);
        }

        return $config;
    }

    /**
     * @param array $config
     */
    private function fixConfigEscapedValues(array $config)
    {
        if (isset($config['protectedSource'])) {
            foreach ($config['protectedSource'] as $key => $value) {
                $this->jsonBuilder->setValue(sprintf('[protectedSource][%s]', $key), $value, false);
            }
        }

        $escapedValueKeys = [
            'stylesheetParser_skipSelectors',
            'stylesheetParser_validSelectors',
        ];

        foreach ($escapedValueKeys as $escapedValueKey) {
            if (isset($config[$escapedValueKey])) {
                $this->jsonBuilder->setValue(sprintf('[%s]', $escapedValueKey), $config[$escapedValueKey], false);
            }
        }
    }

    /**
     * @param string $json
     *
     * @return string
     */
    private function fixConfigConstants($json)
    {
        return preg_replace('/"(CKEDITOR\.[A-Z_]+)"/', '$1', $json);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function fixPath($path)
    {
        if (($position = strpos($path, '?')) !== false) {
            return substr($path, 0, $position);
        }

        return $path;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function fixUrl($url)
    {
        $assetsHelper = $this->getAssets();

        return $assetsHelper !== null ? $assetsHelper->getUrl($url) : $url;
    }

    /**
     * @return string|null
     */
    private function getLanguage()
    {
        if (($request = $this->getRequest()) !== null) {
            return $request->getLocale();
        }

        if ($this->container->hasParameter($parameter = 'locale')) {
            return $this->container->getParameter($parameter);
        }
    }

    /**
     * @return Request|null
     */
    private function getRequest()
    {
        return $this->container->get('request_stack')->getMasterRequest();
    }

    /**
     * @return \Twig_Environment|EngineInterface
     */
    private function getTemplating()
    {
        return $this->container->has($templating = 'twig')
            ? $this->container->get($templating)
            : $this->container->get('templating');
    }

    /**
     * @return Packages|null
     */
    private function getAssets()
    {
        return $this->container->get('assets.packages', ContainerInterface::NULL_ON_INVALID_REFERENCE);
    }

    /**
     * @return RouterInterface
     */
    private function getRouter()
    {
        return $this->container->get('router');
    }
}
