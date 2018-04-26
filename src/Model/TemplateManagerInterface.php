<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Model;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
interface TemplateManagerInterface
{
    /**
     * @return bool
     */
    public function hasTemplates();

    /**
     * @return array
     */
    public function getTemplates();

    /**
     * @param array $templates
     */
    public function setTemplates(array $templates);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasTemplate($name);

    /**
     * @param string $name
     *
     * @return array
     */
    public function getTemplate($name);

    /**
     * @param string $name
     * @param array  $template
     */
    public function setTemplate($name, array $template);
}
