<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Twig;

use Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorExtension extends \Twig_Extension implements CKEditorRendererInterface
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

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $options = ['is_safe' => ['html']];

        return [
            new \Twig_SimpleFunction('ckeditor_base_path', [$this, 'renderBasePath'], $options),
            new \Twig_SimpleFunction('ckeditor_js_path', [$this, 'renderJsPath'], $options),
            new \Twig_SimpleFunction('ckeditor_widget', [$this, 'renderWidget'], $options),
            new \Twig_SimpleFunction('ckeditor_destroy', [$this, 'renderDestroy'], $options),
            new \Twig_SimpleFunction('ckeditor_plugin', [$this, 'renderPlugin'], $options),
            new \Twig_SimpleFunction('ckeditor_styles_set', [$this, 'renderStylesSet'], $options),
            new \Twig_SimpleFunction('ckeditor_template', [$this, 'renderTemplate'], $options),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function renderBasePath($basePath)
    {
        return $this->renderer->renderBasePath($basePath);
    }

    /**
     * {@inheritdoc}
     */
    public function renderJsPath($jsPath)
    {
        return $this->renderer->renderJsPath($jsPath);
    }

    /**
     * {@inheritdoc}
     */
    public function renderWidget($id, array $config, array $options = [])
    {
        return $this->renderer->renderWidget($id, $config, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function renderDestroy($id)
    {
        return $this->renderer->renderDestroy($id);
    }

    /**
     * {@inheritdoc}
     */
    public function renderPlugin($name, array $plugin)
    {
        return $this->renderer->renderPlugin($name, $plugin);
    }

    /**
     * {@inheritdoc}
     */
    public function renderStylesSet($name, array $stylesSet)
    {
        return $this->renderer->renderStylesSet($name, $stylesSet);
    }

    /**
     * {@inheritdoc}
     */
    public function renderTemplate($name, array $template)
    {
        return $this->renderer->renderTemplate($name, $template);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ivory_ckeditor';
    }
}
