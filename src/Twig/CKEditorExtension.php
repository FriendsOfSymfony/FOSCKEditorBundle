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

namespace FOS\CKEditorBundle\Twig;

use FOS\CKEditorBundle\Renderer\CKEditorRendererInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
final class CKEditorExtension extends AbstractExtension implements CKEditorRendererInterface
{
    /**
     * @var CKEditorRendererInterface
     */
    private $renderer;

    /**
     * @param CKEditorRendererInterface $renderer
     */
    public function __construct(CKEditorRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function getFunctions(): array
    {
        $options = ['is_safe' => ['html']];

        return [
            new TwigFunction('ckeditor_base_path', [$this, 'renderBasePath'], $options),
            new TwigFunction('ckeditor_js_path', [$this, 'renderJsPath'], $options),
            new TwigFunction('ckeditor_widget', [$this, 'renderWidget'], $options),
            new TwigFunction('ckeditor_destroy', [$this, 'renderDestroy'], $options),
            new TwigFunction('ckeditor_plugin', [$this, 'renderPlugin'], $options),
            new TwigFunction('ckeditor_styles_set', [$this, 'renderStylesSet'], $options),
            new TwigFunction('ckeditor_template', [$this, 'renderTemplate'], $options),
        ];
    }

    public function renderBasePath(string $basePath): string
    {
        return $this->renderer->renderBasePath($basePath);
    }

    public function renderJsPath(string $jsPath): string
    {
        return $this->renderer->renderJsPath($jsPath);
    }

    public function renderWidget(string $id, array $config, array $options = []): string
    {
        return $this->renderer->renderWidget($id, $config, $options);
    }

    public function renderDestroy(string $id): string
    {
        return $this->renderer->renderDestroy($id);
    }

    public function renderPlugin(string $name, array $plugin): string
    {
        return $this->renderer->renderPlugin($name, $plugin);
    }

    public function renderStylesSet(string $name, array $stylesSet): string
    {
        return $this->renderer->renderStylesSet($name, $stylesSet);
    }

    public function renderTemplate(string $name, array $template): string
    {
        return $this->renderer->renderTemplate($name, $template);
    }

    public function getName(): string
    {
        return 'fos_ckeditor';
    }
}
