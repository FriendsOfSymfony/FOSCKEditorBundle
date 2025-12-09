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

    public function __construct(CKEditorRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function getFunctions(): array
    {
        $options = ['is_safe' => ['html']];

        return [
            new TwigFunction('ckeditor_translation_path', [$this, 'renderTranslationPath'], $options),
            new TwigFunction('ckeditor_js_path', [$this, 'renderJsPath'], $options),
            new TwigFunction('ckeditor_widget', [$this, 'renderWidget'], $options),
            new TwigFunction('ckeditor_size', [$this, 'renderSize'], $options),
        ];
    }

    public function renderTranslationPath(string $basePath): string
    {
        return $this->renderer->renderTranslationPath($basePath);
    }

    public function renderJsPath(string $jsPath): string
    {
        return $this->renderer->renderJsPath($jsPath);
    }

    public function renderSize(array $config): string
    {
        return $this->renderer->renderSize($config);
    }

    public function renderWidget(string $id, array $config, array $options = []): string
    {
        return $this->renderer->renderWidget($id, $config, $options);
    }

    public function getName(): string
    {
        return 'fos_ckeditor';
    }
}
