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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
final class CKEditorRenderer implements CKEditorRendererInterface
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

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var string|null
     */
    private $locale;

    public function __construct(
        JsonBuilder $jsonBuilder,
        RouterInterface $router,
        Packages $packages,
        RequestStack $requestStack,
        Environment $twig,
        $locale = null
    ) {
        $this->jsonBuilder = $jsonBuilder;
        $this->router = $router;
        $this->assetsPackages = $packages;
        $this->twig = $twig;
        $this->requestStack = $requestStack;
        $this->locale = $locale;
    }

    public function renderBasePath(string $basePath): string
    {
        return $this->fixPath($basePath);
    }

    public function renderJsPath(string $jsPath): string
    {
        return $this->fixPath($jsPath);
    }

    public function renderWidget(string $id, array $config, array $options = []): string
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

    public function renderDestroy(string $id): string
    {
        return sprintf(
            'if (CKEDITOR.instances["%1$s"]) { '.
            'CKEDITOR.instances["%1$s"].destroy(true); '.
            'delete CKEDITOR.instances["%1$s"]; '.
            '}',
            $id
        );
    }

    public function renderPlugin(string $name, array $plugin): string
    {
        return sprintf(
            'CKEDITOR.plugins.addExternal("%s", "%s", "%s");',
            $name,
            $this->fixPath($plugin['path']),
            $plugin['filename']
        );
    }

    public function renderStylesSet(string $name, array $stylesSet): string
    {
        return sprintf(
            'if (CKEDITOR.stylesSet.get("%1$s") === null) { '.
            'CKEDITOR.stylesSet.add("%1$s", %2$s); '.
            '}',
            $name,
            $this->jsonBuilder->reset()->setValues($stylesSet)->build()
        );
    }

    public function renderTemplate(string $name, array $template): string
    {
        if (isset($template['imagesPath'])) {
            $template['imagesPath'] = $this->fixPath($template['imagesPath']);
        }

        if (isset($template['templates'])) {
            foreach ($template['templates'] as &$rawTemplate) {
                if (isset($rawTemplate['template'])) {
                    $rawTemplate['html'] = $this->twig->render(
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

    private function fixConfigLanguage(array $config): array
    {
        if (!isset($config['language']) && null !== ($language = $this->getLanguage())) {
            $config['language'] = $language;
        }

        if (isset($config['language'])) {
            $config['language'] = strtolower(str_replace('_', '-', $config['language']));
        }

        return $config;
    }

    private function fixConfigContentsCss(array $config): array
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

    private function fixConfigFilebrowsers(array $config, array $filebrowsers): array
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

    private function fixConfigEscapedValues(JsonBuilder $builder, array $config): void
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

    private function fixConfigConstants(string $json): string
    {
        return preg_replace('/"(CKEDITOR\.[A-Z_]+)"/', '$1', $json);
    }

    private function fixPath(string $path): string
    {
        if (null === $this->assetsPackages) {
            return $path;
        }

        $url = $this->assetsPackages->getUrl($path);

        if ('/' === substr($path, -1) && false !== ($position = strpos($url, '?'))) {
            $url = substr($url, 0, (int) $position);
        }

        return $url;
    }

    private function getLanguage(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request && '' !== $request->getLocale()) {
            return $request->getLocale();
        }

        return $this->locale;
    }
}
