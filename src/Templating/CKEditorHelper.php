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

namespace FOS\CKEditorBundle\Templating;

use FOS\CKEditorBundle\Renderer\CKEditorRendererInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
class CKEditorHelper extends Helper implements CKEditorRendererInterface
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
        return 'fos_ckeditor';
    }
}
