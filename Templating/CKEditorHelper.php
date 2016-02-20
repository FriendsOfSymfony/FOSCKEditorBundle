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

use Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * CKEditor helper.
 *
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
class CKEditorHelper extends Helper implements CKEditorRendererInterface
{
    /** @var \Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface */
    private $renderer;

    /**
     * Creates a CKEditor template helper.
     *
     * @param \Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface $renderer The CKEditor renderer.
     */
    public function __construct($renderer)
    {
        if ($renderer instanceof ContainerInterface) {
            @trigger_error(sprintf(
                'Passing a "%s" to the "%s" constructor has been deprecated in IvoryCKEditorBundle 4.0 and will be removed in 5.0. Use the "%s" instead.',
                'Symfony\Component\DependencyInjection\ContainerInterface',
                'Ivory\CKEditorBundle\Templating\CKEditorHelper',
                'Ivory\CKEditorBundle\Renderer\CKEditorRenderer'
            ), E_USER_DEPRECATED);

            $renderer = $renderer->get('ivory_ck_editor.renderer');
        }

        if (!$renderer instanceof CKEditorRendererInterface) {
            throw new \InvalidArgumentException(sprintf(
                'The CKEditor renderer must be an instance of "%s".',
                'Ivory\CKEditorBundle\Renderer\CKEditorRendererInterface'
            ));
        }

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
    public function renderWidget($id, array $config, array $options = array())
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
