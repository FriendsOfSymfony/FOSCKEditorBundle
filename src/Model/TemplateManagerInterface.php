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

namespace FOS\CKEditorBundle\Model;

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
