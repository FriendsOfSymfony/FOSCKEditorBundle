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

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface CKEditorRendererInterface
{
    /**
     * Renders the base path.
     *
     * @param string $basePath The base path.
     *
     * @return string The rendered base path.
     */
    public function renderBasePath($basePath);

    /**
     * Renders the js path.
     *
     * @param string $jsPath The js path.
     *
     * @return string The rendered js path.
     */
    public function renderJsPath($jsPath);

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
    public function renderWidget($id, array $config, array $options = array());

    /**
     * Renders the destroy.
     *
     * @param string $id The identifier.
     *
     * @return string The rendered destroy.
     */
    public function renderDestroy($id);

    /**
     * Renders a plugin.
     *
     * @param string $name   The name.
     * @param array  $plugin The plugin.
     *
     * @return string The rendered plugin.
     */
    public function renderPlugin($name, array $plugin);

    /**
     * Renders a styles set.
     *
     * @param string $name      The name
     * @param array  $stylesSet The style set.
     *
     * @return string The rendered style set.
     */
    public function renderStylesSet($name, array $stylesSet);

    /**
     * Renders a template.
     *
     * @param string $name     The template name.
     * @param array  $template The template.
     *
     * @return string The rendered template.
     */
    public function renderTemplate($name, array $template);
}
