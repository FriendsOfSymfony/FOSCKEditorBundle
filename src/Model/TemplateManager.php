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

use FOS\CKEditorBundle\Exception\TemplateManagerException;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class TemplateManager implements TemplateManagerInterface
{
    /**
     * @var array
     */
    private $templates = [];

    /**
     * @param array $templates
     */
    public function __construct(array $templates = [])
    {
        $this->setTemplates($templates);
    }

    /**
     * {@inheritdoc}
     */
    public function hasTemplates()
    {
        return !empty($this->templates);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplates(array $templates)
    {
        foreach ($templates as $name => $template) {
            $this->setTemplate($name, $template);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasTemplate($name)
    {
        return isset($this->templates[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate($name)
    {
        if (!$this->hasTemplate($name)) {
            throw TemplateManagerException::templateDoesNotExist($name);
        }

        return $this->templates[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplate($name, array $template)
    {
        $this->templates[$name] = $template;
    }
}
