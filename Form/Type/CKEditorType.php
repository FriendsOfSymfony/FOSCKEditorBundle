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
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormView,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
    public function buildForm(FormBuilderInterface $builder, array $options)
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
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'config'  => $form->getAttribute('config'),
            'plugins' => $form->getAttribute('plugins'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'required'    => false,
            'config_name' => null,
            'config'      => array(),
            'plugins'     => array(),
        ));

        $resolver->addAllowedValues(array('required' => array(false)));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
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
