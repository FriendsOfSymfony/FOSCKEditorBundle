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

namespace FOS\CKEditorBundle\Renderer;

use FOS\CKEditorBundle\Builder\JsonBuilder;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
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
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Packages
     */
    private $assetsPackages;

    private $templating;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var null|string
     */
    private $locale;

    /**
     * @param JsonBuilder|ContainerInterface $containerOrJsonBuilder
     * @param RouterInterface                $router
     * @param Packages                       $packages
     * @param RequestStack                   $requestStack
     * @param EngineInterface                $templating
     * @param null|string                    $locale
     */
    public function __construct(
        $containerOrJsonBuilder,
        RouterInterface $router = null,
        Packages $packages = null,
        RequestStack $requestStack = null,
        $templating = null,
        $locale = null
    ) {
        if ($containerOrJsonBuilder instanceof ContainerInterface) {
            @trigger_error(sprintf(
                'Passing a %s as %s first argument is deprecated since FOSCKEditor 1.0, and will be removed in 2.0.'
                .' Use %s instead.',
                ContainerInterface::class,
                __METHOD__,
                JsonBuilder::class
            ), E_USER_DEPRECATED);
            $jsonBuilder = $containerOrJsonBuilder->get('fos_ck_editor.renderer.json_builder');
            $router = $containerOrJsonBuilder->get('router');
            $packages = $containerOrJsonBuilder->get('assets.packages');
            $requestStack = $containerOrJsonBuilder->get('request_stack');
            $templating = $containerOrJsonBuilder->has('twig')
                ? $containerOrJsonBuilder->get('twig')
                : $containerOrJsonBuilder->get('templating');
        } elseif ($containerOrJsonBuilder instanceof JsonBuilder) {
            $jsonBuilder = $containerOrJsonBuilder;
            if (null === $router) {
                throw new \InvalidArgumentException(sprintf(
                    '%s 2nd argument must not be null when using %s as first argument',
                    __METHOD__,
                    JsonBuilder::class
                ));
            } elseif (null === $packages) {
                throw new \InvalidArgumentException(sprintf(
                    '%s 3rd argument must not be null when using %s as first argument',
                    __METHOD__,
                    JsonBuilder::class
                ));
            } elseif (null === $requestStack) {
                throw new \InvalidArgumentException(sprintf(
                    '%s 4th argument must not be null when using %s as first argument',
                    __METHOD__,
                    JsonBuilder::class
                ));
            } elseif (null === $templating) {
                throw new \InvalidArgumentException(sprintf(
                    '%s 5th argument must not be null when using %s as first argument',
                    __METHOD__,
                    JsonBuilder::class
                ));
            }
        } else {
            throw new \InvalidArgumentException(sprintf(
                '%s first argument must be an instance of %s or %s (%s given).',
                __METHOD__,
                ContainerInterface::class,
                JsonBuilder::class,
                is_object($containerOrJsonBuilder)
                    ? get_class($containerOrJsonBuilder)
                    : gettype($containerOrJsonBuilder)
            ));
        }

        $this->jsonBuilder = $jsonBuilder;
        $this->router = $router;
        $this->assetsPackages = $packages;
        $this->templating = $templating;
        $this->requestStack = $requestStack;
        $this->locale = $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function renderBasePath($basePath)
    {
        return $this->fixPath($basePath);
    }

    /**
     * {@inheritdoc}
     */
    public function renderJsPath($jsPath)
    {
        return $this->fixPath($jsPath);
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

        $autoInline = isset($options['auto_inline']) && !$options['auto_inline']
            ? 'CKEDITOR.disableAutoInline = true;'."\n"
            : null;

        $builder = $this->jsonBuilder->reset()->setValues($config);
        $this->fixConfigEscapedValues($builder, $config);

        $widget = sprintf(
            'CKEDITOR.%s("%s", %s);',
            isset($options['inline']) && $options['inline'] ? 'inline' : 'replace',
            $id,
            $this->fixConfigConstants($builder->build())
        );

        if (isset($options['input_sync']) && $options['input_sync']) {
            $variable = 'fos_ckeditor_'.$id;
            $widget = 'var '.$variable.' = '.$widget."\n";

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
            $this->fixPath($plugin['path']),
            $plugin['filename']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function renderStylesSet($name, array $stylesSet)
    {
        return sprintf(
            'if (CKEDITOR.stylesSet.get("%1$s") === null) { '.
            'CKEDITOR.stylesSet.add("%1$s", %2$s); '.
            '}',
            $name,
            $this->jsonBuilder->reset()->setValues($stylesSet)->build()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function renderTemplate($name, array $template)
    {
        if (isset($template['imagesPath'])) {
            $template['imagesPath'] = $this->fixPath($template['imagesPath']);
        }

        if (isset($template['templates'])) {
            foreach ($template['templates'] as &$rawTemplate) {
                if (isset($rawTemplate['template'])) {
                    $rawTemplate['html'] = $this->templating->render(
                        $rawTemplate['template'],
                        isset($rawTemplate['template_parameters']) ? $rawTemplate['template_parameters'] : []
                    );
                }

                unset($rawTemplate['template'], $rawTemplate['template_parameters']);
            }
        }

        return sprintf(
            'CKEDITOR.addTemplates("%s", %s);',
            $name,
            $this->jsonBuilder->reset()->setValues($template)->build()
        );
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function fixConfigLanguage(array $config)
    {
        if (!isset($config['language']) && null !== ($language = $this->getLanguage())) {
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
                $config['contentsCss'][] = $this->fixPath($cssContent);
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
                $config[$url] = $config[$handler]($this->router);
            } elseif (isset($config[$route])) {
                $config[$url] = $this->router->generate(
                    $config[$route],
                    isset($config[$routeParameters]) ? $config[$routeParameters] : [],
                    isset($config[$routeType]) ? $config[$routeType] : UrlGeneratorInterface::ABSOLUTE_PATH
                );
            }

            unset($config[$handler], $config[$route], $config[$routeParameters], $config[$routeType]);
        }

        return $config;
    }

    /**
     * @param JsonBuilder $builder
     * @param array       $config
     */
    private function fixConfigEscapedValues(JsonBuilder $builder, array $config)
    {
        if (isset($config['protectedSource'])) {
            foreach ($config['protectedSource'] as $key => $value) {
                $builder->setValue(sprintf('[protectedSource][%s]', $key), $value, false);
            }
        }

        $escapedValueKeys = [
            'stylesheetParser_skipSelectors',
            'stylesheetParser_validSelectors',
        ];

        foreach ($escapedValueKeys as $escapedValueKey) {
            if (isset($config[$escapedValueKey])) {
                $builder->setValue(sprintf('[%s]', $escapedValueKey), $config[$escapedValueKey], false);
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
        if (null === $this->assetsPackages) {
            return $path;
        }

        $url = $this->assetsPackages->getUrl($path);

        if ('/' === substr($path, -1) && false !== ($position = strpos($url, '?'))) {
            $url = substr($url, 0, $position);
        }

        return $url;
    }

    /**
     * @return null|string
     */
    private function getLanguage()
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null !== $request) {
            return $request->getLocale();
        }

        return $this->locale;
    }
}
