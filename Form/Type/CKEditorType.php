<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Form\Type;

use Ivory\CKEditorBundle\Model\ConfigManagerInterface,
    Ivory\CKEditorBundle\Model\PluginManagerInterface,
    Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder,
    Symfony\Component\Form\FormView,
    Symfony\Component\Form\FormInterface;

/**
 * CKEditor type.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorType extends AbstractType
{
    /** @var \Ivory\CKEditorBundle\Model\ConfigManagerInterface */
    protected $configManager;

    /** @var \Ivory\CKEditorBundle\Model\PluginManagerInterface */
    protected $pluginManager;

    /**
     * Creates a CKEditor type.
     *
     * @param \Ivory\CKEditorBundle\Model\ConfigManagerInterface $configManager The CKEditor config manager.
     * @param \Ivory\CKEditorBundle\Model\PluginManagerInterface $pluginManager The CKEditor plugin manager.
     */
    public function __construct(ConfigManagerInterface $configManager, PluginManagerInterface $pluginManager)
    {
        $this->configManager = $configManager;
        $this->pluginManager = $pluginManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $config = $options['config'];

        if ($options['config_name'] === null) {
            $name = uniqid('ivory', true);

            $options['config_name'] = $name;
            $this->configManager->setConfig($name, $config);
        } else {
            $this->configManager->mergeConfig($options['config_name'], $config);
        }

        $builder->setAttribute('config', $this->configManager->getConfig($options['config_name']));
        $builder->setAttribute('plugins', array_merge($this->pluginManager->getPlugins(), $options['plugins']));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $view->set('config', $form->getAttribute('config'));
        $view->set('plugins', $form->getAttribute('plugins'));
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'required'    => false,
            'config_name' => null,
            'config'      => array(),
            'plugins'     => array(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedOptionValues(array $options)
    {
        return array('required' => array(false));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ckeditor';
    }
}
