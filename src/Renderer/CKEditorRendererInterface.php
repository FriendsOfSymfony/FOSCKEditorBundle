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

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface CKEditorRendererInterface
{
    /**
     * @param string $basePath
     *
     * @return string
     */
    public function renderBasePath($basePath);

    /**
     * @param string $jsPath
     *
     * @return string
     */
    public function renderJsPath($jsPath);

    /**
     * @param string $id
     * @param array  $config
     * @param array  $options
     *
     * The available options are:
     *  - auto_inline: bool
     *  - inline: bool
     *  - input_sync: bool
     *
     * @return string
     */
    public function renderWidget($id, array $config, array $options = []);

    /**
     * @param string $id
     *
     * @return string
     */
    public function renderDestroy($id);

    /**
     * @param string $name
     * @param array  $plugin
     *
     * @return string
     */
    public function renderPlugin($name, array $plugin);

    /**
     * @param string $name
     * @param array  $stylesSet
     *
     * @return string
     */
    public function renderStylesSet($name, array $stylesSet);

    /**
     * @param string $name
     * @param array  $template
     *
     * @return string
     */
    public function renderTemplate($name, array $template);
}
