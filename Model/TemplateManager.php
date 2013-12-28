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

use Ivory\CKEditorBundle\Exception\TemplateManagerException;
use Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper;
use Symfony\Component\Templating\Helper\CoreAssetsHelper;

/**
 * {@inheritdoc}
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TemplateManager implements TemplateManagerInterface
{
    /** @var \Symfony\Component\Templating\Helper\CoreAssetsHelper */
    protected $assetsHelper;

    /** @var \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper */
    protected $assetsVersionTrimerHelper;

    /** @var array */
    protected $templates = array();

    /**
     * Creates a plugin manager.
     *
     * @param \Symfony\Component\Templating\Helper\CoreAssetsHelper  $assetsHelper              The assets helper.
     * @param \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper $assetsVersionTrimerHelper The version trimer.
     * @param array                                                  $templates                 The CKEditor templates.
     */
    public function __construct(
        CoreAssetsHelper $assetsHelper,
        AssetsVersionTrimerHelper $assetsVersionTrimerHelper,
        array $templates = array()
    ) {
        $this->setAssetsHelper($assetsHelper);
        $this->setAssetsVersionTrimerHelper($assetsVersionTrimerHelper);
        $this->setTemplates($templates);
    }

    /**
     * Gets the assets helper.
     *
     * @return \Symfony\Component\Templating\Helper\CoreAssetsHelper The assets helper.
     */
    public function getAssetsHelper()
    {
        return $this->assetsHelper;
    }

    /**
     * Sets the assets helper.
     *
     * @param \Symfony\Component\Templating\Helper\CoreAssetsHelper $assetsHelper The assets helper.
     */
    public function setAssetsHelper(CoreAssetsHelper $assetsHelper)
    {
        $this->assetsHelper = $assetsHelper;
    }

    /**
     * Gets the assets version trimer helper.
     *
     * @return \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper The assets version trimer helper.
     */
    public function getAssetsVersionTrimerHelper()
    {
        return $this->assetsVersionTrimerHelper;
    }

    /**
     * Sets the assets version trimer helper.
     *
     * @param \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper $assetsVersionTrimerHelper The version trimer.
     */
    public function setAssetsVersionTrimerHelper(AssetsVersionTrimerHelper $assetsVersionTrimerHelper)
    {
        $this->assetsVersionTrimerHelper = $assetsVersionTrimerHelper;
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
        if (isset($template['imagesPath'])) {
            $template['imagesPath'] = $this->assetsVersionTrimerHelper->trim(
                $this->assetsHelper->getUrl($template['imagesPath'])
            );
        }

        $this->templates[$name] = $template;
    }
}
