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
use FOS\CKEditorBundle\Installer\CKEditorPredefinedBuild;
use Symfony\Component\Asset\Packages;
use Symfony\Component\HttpFoundation\RequestStack;
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

    public function renderTranslationPath(string $basePath): string
    {
        return $this->fixPath($basePath) . 'translations/' .  $this->getLanguage() . '.js';
    }

    public function renderJsPath(string $jsPath): string
    {
        return $this->fixPath($jsPath);
    }

    public function renderWidget(string $id, array $config, array $options = []): string
    {
        $config = $this->fixConfigLanguage($config);

        // add plugins
        // todo not possible to add plugins to builds
        // @link https://ckeditor.com/docs/ckeditor5/latest/support/error-codes.html#error-ckeditor-duplicated-modules
        // @link https://github.com/ckeditor/ckeditor5/issues/14745#issuecomment-1670822989
        // $config = $this->renderPlugins($options, $config);

        $widget = $this->create($id, $config);
        $widget .= $this->catch();

        return $widget;
    }

    public function create(string $selectorName, array $config): string
    {
        if (isset($config['build']) && $config['build']) {
            $release = $config['build'];
        } else {
            $release = CKEditorPredefinedBuild::RELEASE_CLASSIC;
        }

        switch ($release) {
            case CKEditorPredefinedBuild::RELEASE_CLASSIC:
                $name = 'ClassicEditor';
                break;
            case CKEditorPredefinedBuild::RELEASE_BALLOON:
            case CKEditorPredefinedBuild::RELEASE_BALLOON_BLOCK:
                $name = 'BalloonEditor';
                break;
            case CKEditorPredefinedBuild::RELEASE_INLINE:
                $name = 'InlineEditor';
                break;
            case CKEditorPredefinedBuild::RELEASE_DOCUMENT:
                $name = 'DecoupledEditor';
                break;
            case CKEditorPredefinedBuild::RELEASE_CUSTOM:
                // todo
                $name = 'todo';
                break;
            default:
                $name = null;
        }

        $builder = $this->jsonBuilder->reset()->setValues($config);

        return sprintf(
            '%s.create(document.querySelector(\'#%s\'), %s)',
            $name,
            $selectorName,
            $builder->build()
        );
    }

    public function catch(): string
    {
        return '.catch((error) => {
            console.error(error);
        });';
    }

    private function renderPlugins(array $options, array $config): array
    {
        if (isset($options['plugins']) && $options['plugins'] && count($options['plugins']) > 0) {
            foreach ($options['plugins'] as $pluginName => $plugin) {
                $config = $this->renderPlugin($pluginName, $plugin, $config);
            }
        }

        return $config;
    }

    private function renderPlugin(string $name, array $plugin, array $config): array
    {
        if(!isset($config['extraPlugins'])) {
            $config['extraPlugins'] = [
                $name,
            ];
        } else {
            array_push($config['extraPlugins'], $name);
        }

        return $config;
    }

//    public function renderTemplate(string $name, array $template): string
//    {
//        if (isset($template['imagesPath'])) {
//            $template['imagesPath'] = $this->fixPath($template['imagesPath']);
//        }
//
//        if (isset($template['templates'])) {
//            foreach ($template['templates'] as &$rawTemplate) {
//                if (isset($rawTemplate['template'])) {
//                    $rawTemplate['html'] = $this->twig->render(
//                        $rawTemplate['template'],
//                        isset($rawTemplate['template_parameters']) ? $rawTemplate['template_parameters'] : []
//                    );
//                }
//
//                unset($rawTemplate['template'], $rawTemplate['template_parameters']);
//            }
//        }
//
//        return sprintf(
//            'CKEDITOR.addTemplates("%s", %s);',
//            $name,
//            $this->jsonBuilder->reset()->setValues($template)->build()
//        );
//    }

    public function renderSize(array $config): string
    {
        $height = '';
        $width = '';
        $minHeight = '';
        $minWidth = '';
        if (isset($config['height'])) {
            $height = 'height: ' . $config['height'] . ';';
            unset($config['height']);
        }
        if (isset($config['width'])) {
            $width = 'width: ' . $config['width'] . ';';
            unset($config['width']);
        }
        if (isset($config['minHeight'])) {
            $minHeight = 'min-height: ' . $config['minHeight'] . ';';
            unset($config['minHeight']);
        }
        if (isset($config['minWidth'])) {
            $minWidth = 'min-width: ' . $config['minWidth'] . ';';
            unset($config['minWidth']);
        }

        return '.ck.ck-editor__editable { ' . $height . $width . $minHeight . $minWidth . ' }';
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

    private function getLanguage(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request && '' !== $request->getLocale()) {
            return $request->getLocale();
        }

        return $this->locale;
    }

//    private function fixConfigConstants(string $json): string
//    {
//        return preg_replace('/"(CKEDITOR\.[A-Z_]+)"/', '$1', $json);
//    }

    private function fixPath(string $path): string
    {
        if (null === $this->assetsPackages) {
            return $path;
        }

        $url = $this->assetsPackages->getUrl($path);

        if ('/' === substr($path, -1) && false !== ($position = strpos($url, '?'))) {
            $url = substr($url, 0, (int)$position);
        }

        return $url;
    }
}
