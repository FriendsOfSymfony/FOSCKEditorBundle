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

use Ivory\CKEditorBundle\Model\ConfigManagerInterface;
use Ivory\CKEditorBundle\Model\PluginManagerInterface;
use Ivory\CKEditorBundle\Model\StylesSetManagerInterface;
use Ivory\CKEditorBundle\Model\TemplateManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * CKEditor type.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorType extends AbstractType
{
    /** @var boolean */
    private $enable = true;

    /** @var boolean */
    private $autoload = true;

    /** @var boolean */
    private $autoInline = true;

    /** @var boolean */
    private $inline = false;

    /** @var boolean */
    private $jquery = false;

    /** @var boolean */
    private $inputSync = false;

    /** @var string */
    private $basePath = 'bundles/ivoryckeditor/';

    /** @var string */
    private $jsPath = 'bundles/ivoryckeditor/ckeditor.js';

    /** @var string */
    private $jqueryPath = 'bundles/ivoryckeditor/adapters/jquery.js';

    /** @var \Ivory\CKEditorBundle\Model\ConfigManagerInterface */
    private $configManager;

    /** @var \Ivory\CKEditorBundle\Model\PluginManagerInterface */
    private $pluginManager;

    /** @var \Ivory\CKEditorBundle\Model\StylesSetManagerInterface */
    private $stylesSetManager;

    /** @var \Ivory\CKEditorBundle\Model\TemplateManagerInterface */
    private $templateManager;

    /**
     * Creates a CKEditor type.
     *
     * @param \Ivory\CKEditorBundle\Model\ConfigManagerInterface    $configManager    The config manager.
     * @param \Ivory\CKEditorBundle\Model\PluginManagerInterface    $pluginManager    The plugin manager.
     * @param \Ivory\CKEditorBundle\Model\StylesSetManagerInterface $stylesSetManager The styles set manager.
     * @param \Ivory\CKEditorBundle\Model\TemplateManagerInterface  $templateManager  The template manager.
     */
    public function __construct(
        ConfigManagerInterface $configManager,
        PluginManagerInterface $pluginManager,
        StylesSetManagerInterface $stylesSetManager,
        TemplateManagerInterface $templateManager
    ) {
        $this->setConfigManager($configManager);
        $this->setPluginManager($pluginManager);
        $this->setStylesSetManager($stylesSetManager);
        $this->setTemplateManager($templateManager);
    }

    /**
     * Sets/Checks if the widget is enabled.
     *
     * @param boolean|null $enable TRUE if the widget is enabled else FALSE.
     *
     * @return boolean TRUE if the widget is enabled else FALSE.
     */
    public function isEnable($enable = null)
    {
        if ($enable !== null) {
            $this->enable = (bool) $enable;
        }

        return $this->enable;
    }

    /**
     * Sets/Checks if the widget is autoloaded.
     *
     * @param boolean $autoload TRUE if the widget is autoloaded else FALSE.
     *
     * @return boolean TRUE if the widget is autoloaded else FALSE.
     */
    public function isAutoload($autoload = null)
    {
        if ($autoload !== null) {
            $this->autoload = (bool) $autoload;
        }

        return $this->autoload;
    }

    /**
     * Sets/Checks if the widget is auto inlined.
     *
     * @param boolean $autoInline TRUE if the widget is auto inlined else FALSE.
     *
     * @return boolean TRUE if the widget is auto inlined else FALSE.
     */
    public function isAutoInline($autoInline = null)
    {
        if ($autoInline !== null) {
            $this->autoInline = (bool) $autoInline;
        }

        return $this->autoInline;
    }

    /**
     * Sets/Checks if the widget is inlined.
     *
     * @param boolean $inline TRUE if the widget is inlined else FALSE.
     *
     * @return boolean TRUE if the widget is inlined else FALSE.
     */
    public function isInline($inline = null)
    {
        if ($inline !== null) {
            $this->inline = (bool) $inline;
        }

        return $this->inline;
    }

    /**
     * Checks/Sets if the jquery adapter is loaded.
     *
     * @param boolean $jquery TRUE if the jquery adapter is loaded else FALSE.
     *
     * @return boolean TRUE if the jquery adapter is loaded else FALSE.
     */
    public function useJquery($jquery = null)
    {
        if ($jquery !== null) {
            $this->jquery = (bool) $jquery;
        }

        return $this->jquery;
    }

    /**
     * Sets/Checks if the input is synchonized with the widget.
     *
     * @param boolean $inputSync TRUE if the input is synchronized with the widget else FALSE.
     *
     * @return boolean TRUE if the input is synchronized with the widget else FALSE.
     */
    public function isInputSync($inputSync = null)
    {
        if ($inputSync !== null) {
            $this->inputSync = (bool) $inputSync;
        }

        return $this->inputSync;
    }

    /**
     * Gets the CKEditor base path.
     *
     * @return string The CKEditor base path.
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Sets the CKEditor base path.
     *
     * @param string $basePath The CKEditor base path.
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * Gets the CKEditor JS path.
     *
     * @return string The CKEditor JS path.
     */
    public function getJsPath()
    {
        return $this->jsPath;
    }

    /**
     * Sets the CKEditor JS path.
     *
     * @param string $jsPath The CKEditor JS path.
     */
    public function setJsPath($jsPath)
    {
        $this->jsPath = $jsPath;
    }

    /**
     * Gets the jquery path.
     *
     * @return string The jquery path.
     */
    public function getJqueryPath()
    {
        return $this->jqueryPath;
    }

    /**
     * Sets the jquery path.
     *
     * @param string $jqueryPath The jquery path.
     */
    public function setJqueryPath($jqueryPath)
    {
        $this->jqueryPath = $jqueryPath;
    }

    /**
     * Gets the CKEditor config manager.
     *
     * @return \Ivory\CKEditorBundle\Model\ConfigManagerInterface The CKEditor config manager.
     */
    public function getConfigManager()
    {
        return $this->configManager;
    }

    /**
     * Sets the CKEditor config manager.
     *
     * @param \Ivory\CKEditorBundle\Model\ConfigManagerInterface $configManager The CKEditor config manager.
     */
    public function setConfigManager(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * Gets the CKEditor plugin manager.
     *
     * @return \Ivory\CKEditorBundle\Model\PluginManagerInterface The CKEditor plugin manager.
     */
    public function getPluginManager()
    {
        return $this->pluginManager;
    }

    /**
     * Sets the CKEditor plugin manager.
     *
     * @param \Ivory\CKEditorBundle\Model\PluginManagerInterface $pluginManager The CKEditor plugin manager.
     */
    public function setPluginManager(PluginManagerInterface $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    /**
     * Gets the styles set manager.
     *
     * @return \Ivory\CKEditorBundle\Model\StylesSetManagerInterface The styles set manager.
     */
    public function getStylesSetManager()
    {
        return $this->stylesSetManager;
    }

    /**
     * Sets the styles set manager.
     *
     * @param \Ivory\CKEditorBundle\Model\StylesSetManagerInterface $stylesSetManager The styles set manager.
     */
    public function setStylesSetManager(StylesSetManagerInterface $stylesSetManager)
    {
        $this->stylesSetManager = $stylesSetManager;
    }

    /**
     * Gets the CKEditor template manager.
     *
     * @return \Ivory\CKEditorBundle\Model\TemplateManagerInterface The CKEditor template manager.
     */
    public function getTemplateManager()
    {
        return $this->templateManager;
    }

    /**
     * Sets the CKEditor template manager.
     *
     * @param \Ivory\CKEditorBundle\Model\TemplateManagerInterface $templateManager The CKEditor template manager.
     */
    public function setTemplateManager(TemplateManagerInterface $templateManager)
    {
        $this->templateManager = $templateManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('enable', $options['enable']);

        if ($builder->getAttribute('enable')) {
            $builder->setAttribute('autoload', $options['autoload']);
            $builder->setAttribute('auto_inline', $options['auto_inline']);
            $builder->setAttribute('inline', $options['inline']);
            $builder->setAttribute('jquery', $options['jquery']);
            $builder->setAttribute('input_sync', $options['input_sync']);
            $builder->setAttribute('base_path', $options['base_path']);
            $builder->setAttribute('js_path', $options['js_path']);
            $builder->setAttribute('jquery_path', $options['jquery_path']);

            $configManager = clone $this->configManager;
            $pluginManager = clone $this->pluginManager;
            $stylesSetManager = clone $this->stylesSetManager;
            $templateManager = clone $this->templateManager;

            $config = $options['config'];
            if ($options['config_name'] === null) {
                $options['config_name'] = uniqid('ivory', true);
                $configManager->setConfig($options['config_name'], $config);
            } else {
                $configManager->mergeConfig($options['config_name'], $config);
            }

            $pluginManager->setPlugins($options['plugins']);
            $stylesSetManager->setStylesSets($options['styles']);
            $templateManager->setTemplates($options['templates']);

            $builder->setAttribute('config', $configManager->getConfig($options['config_name']));
            $builder->setAttribute('plugins', $pluginManager->getPlugins());
            $builder->setAttribute('styles', $stylesSetManager->getStylesSets());
            $builder->setAttribute('templates', $templateManager->getTemplates());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['enable'] = $form->getConfig()->getAttribute('enable');

        if ($form->getConfig()->getAttribute('enable')) {
            $view->vars['autoload'] = $form->getConfig()->getAttribute('autoload');
            $view->vars['auto_inline'] = $form->getConfig()->getAttribute('auto_inline');
            $view->vars['inline'] = $form->getConfig()->getAttribute('inline');
            $view->vars['jquery'] = $form->getConfig()->getAttribute('jquery');
            $view->vars['input_sync'] = $form->getConfig()->getAttribute('input_sync');
            $view->vars['base_path'] = $form->getConfig()->getAttribute('base_path');
            $view->vars['js_path'] = $form->getConfig()->getAttribute('js_path');
            $view->vars['jquery_path'] = $form->getConfig()->getAttribute('jquery_path');
            $view->vars['config'] = $form->getConfig()->getAttribute('config');
            $view->vars['plugins'] = $form->getConfig()->getAttribute('plugins');
            $view->vars['styles'] = $form->getConfig()->getAttribute('styles');
            $view->vars['templates'] = $form->getConfig()->getAttribute('templates');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'enable'      => $this->enable,
                'autoload'    => $this->autoload,
                'auto_inline' => $this->autoInline,
                'inline'      => $this->inline,
                'jquery'      => $this->jquery,
                'input_sync'  => $this->inputSync,
                'base_path'   => $this->basePath,
                'js_path'     => $this->jsPath,
                'jquery_path' => $this->jqueryPath,
                'config_name' => $this->configManager->getDefaultConfig(),
                'config'      => array(),
                'plugins'     => array(),
                'styles'      => array(),
                'templates'   => array(),
            ))
        ;

        $allowedTypesMap = array(
            'enable'      => 'bool',
            'autoload'    => 'bool',
            'auto_inline' => 'bool',
            'inline'      => 'bool',
            'jquery'      => 'bool',
            'input_sync'  => 'bool',
            'config_name' => array('string', 'null'),
            'base_path'   => 'string',
            'js_path'     => 'string',
            'jquery_path' => 'string',
            'config'      => 'array',
            'plugins'     => 'array',
            'styles'      => 'array',
            'templates'   => 'array',
        );

        if (Kernel::VERSION_ID >= 20600) {
            foreach ($allowedTypesMap as $option => $allowedTypes) {
                $resolver->addAllowedTypes($option, $allowedTypes);
            }
        } else {
            $resolver->addAllowedTypes($allowedTypesMap);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        // Prefer the FQCN if the getBlockPrefix method exists on the parent method
        if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            return 'Symfony\Component\Form\Extension\Core\Type\TextareaType';
        }

        // Return the legacy shortname; drop this when Symfony <2.8 support is removed
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'ckeditor';
    }
}
