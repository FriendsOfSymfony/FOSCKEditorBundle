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
use Ivory\CKEditorBundle\Model\ToolbarManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
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
    private $async = false;

    /** @var boolean */
    private $autoload = true;

    /** @var boolean */
    private $autoInline = true;

    /** @var boolean */
    private $inline = false;

    /** @var boolean */
    private $jquery = false;

    /** @var boolean */
    private $requireJs = false;

    /** @var boolean */
    private $inputSync = false;

    /** @var array */
    private $filebrowsers = array();

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

    /** @var \Ivory\CKEditorBundle\Model\ToolbarManagerInterface */
    private $toolbarManager;

    /**
     * Creates a CKEditor type.
     *
     * @param \Ivory\CKEditorBundle\Model\ConfigManagerInterface    $configManager    The config manager.
     * @param \Ivory\CKEditorBundle\Model\PluginManagerInterface    $pluginManager    The plugin manager.
     * @param \Ivory\CKEditorBundle\Model\StylesSetManagerInterface $stylesSetManager The styles set manager.
     * @param \Ivory\CKEditorBundle\Model\TemplateManagerInterface  $templateManager  The template manager.
     * @param \Ivory\CKEditorBundle\Model\ToolbarManagerInterface   $toolbarManager   The toolbar manager.
     */
    public function __construct(
        ConfigManagerInterface $configManager,
        PluginManagerInterface $pluginManager,
        StylesSetManagerInterface $stylesSetManager,
        TemplateManagerInterface $templateManager,
        ToolbarManagerInterface $toolbarManager
    ) {
        $this->setConfigManager($configManager);
        $this->setPluginManager($pluginManager);
        $this->setStylesSetManager($stylesSetManager);
        $this->setTemplateManager($templateManager);
        $this->setToolbarManager($toolbarManager);
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
     * Sets/Checks if the widget is async.
     *
     * @param boolean|null $async TRUE if the widget is async else FALSE.
     *
     * @return boolean TRUE if the widget is async else FALSE.
     */
    public function isAsync($async = null)
    {
        if ($async !== null) {
            $this->async = (bool) $async;
        }

        return $this->async;
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
     * Checks/Sets if require js is used.
     *
     * @param boolean $requireJs TRUE if the requirejs is used else FALSE.
     *
     * @return boolean TRUE if the requirejs is used else FALSE.
     */
    public function useRequireJs($requireJs = null)
    {
        if ($requireJs !== null) {
            $this->requireJs = (bool) $requireJs;
        }

        return $this->requireJs;
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
     * Checks if there are filebrowsers.
     *
     * @return boolean TRUE if there are filebrowsers else FALSE.
     */
    public function hasFilebrowsers()
    {
        return !empty($this->filebrowsers);
    }

    /**
     * Gets the filebrowsers.
     *
     * @return array The filebrowsers.
     */
    public function getFilebrowsers()
    {
        return $this->filebrowsers;
    }

    /**
     * Sets the filebrowsers.
     *
     * @param array $filebrowsers The filebrowsers.
     */
    public function setFilebrowsers(array $filebrowsers)
    {
        foreach ($filebrowsers as $filebrowser) {
            $this->addFilebrowser($filebrowser);
        }
    }

    /**
     * Checks if there is the filebrowser.
     *
     * @param string $filebrowser The filebrowser.
     *
     * @return boolean TRUE if there is the filebrowser else FALSE.
     */
    public function hasFilebrowser($filebrowser)
    {
        return in_array($filebrowser, $this->filebrowsers, true);
    }

    /**
     * Adds a filebrowser.
     *
     * @param string $filebrowser The filebrowser.
     */
    public function addFilebrowser($filebrowser)
    {
        if (!$this->hasFilebrowser($filebrowser)) {
            $this->filebrowsers[] = $filebrowser;
        }
    }

    /**
     * Removes a filebrowser.
     *
     * @param string $filebrowser The filebrowser.
     */
    public function removeFilebrowser($filebrowser)
    {
        unset($this->filebrowsers[array_search($filebrowser, $this->filebrowsers, true)]);
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
     * Gets the CKEditor toolbar manager.
     *
     * @return \Ivory\CKEditorBundle\Model\ToolbarManagerInterface The CKEditor toolbar manager.
     */
    public function getToolbarManager()
    {
        return $this->toolbarManager;
    }

    /**
     * Sets the CKEditor toolbar manager.
     *
     * @param \Ivory\CKEditorBundle\Model\ToolbarManagerInterface $toolbarManager The CKEditor toolbar manager.
     */
    public function setToolbarManager(ToolbarManagerInterface $toolbarManager)
    {
        $this->toolbarManager = $toolbarManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('enable', $options['enable']);

        if ($builder->getAttribute('enable')) {
            $builder->setAttribute('async', $options['async']);
            $builder->setAttribute('autoload', $options['autoload']);
            $builder->setAttribute('auto_inline', $options['auto_inline']);
            $builder->setAttribute('inline', $options['inline']);
            $builder->setAttribute('jquery', $options['jquery']);
            $builder->setAttribute('require_js', $options['require_js']);
            $builder->setAttribute('input_sync', $options['input_sync']);
            $builder->setAttribute('filebrowsers', $options['filebrowsers']);
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

            $config = $configManager->getConfig($options['config_name']);

            if (isset($config['toolbar']) && is_string($config['toolbar'])) {
                $config['toolbar'] = $this->toolbarManager->resolveToolbar($config['toolbar']);
            }

            $builder->setAttribute('config', $config);
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
            $view->vars['async'] = $form->getConfig()->getAttribute('async');
            $view->vars['autoload'] = $form->getConfig()->getAttribute('autoload');
            $view->vars['auto_inline'] = $form->getConfig()->getAttribute('auto_inline');
            $view->vars['inline'] = $form->getConfig()->getAttribute('inline');
            $view->vars['jquery'] = $form->getConfig()->getAttribute('jquery');
            $view->vars['require_js'] = $form->getConfig()->getAttribute('require_js');
            $view->vars['input_sync'] = $form->getConfig()->getAttribute('input_sync');
            $view->vars['filebrowsers'] = $form->getConfig()->getAttribute('filebrowsers');
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
                'enable'       => $this->enable,
                'async'        => $this->async,
                'autoload'     => $this->autoload,
                'auto_inline'  => $this->autoInline,
                'inline'       => $this->inline,
                'jquery'       => $this->jquery,
                'require_js'   => $this->requireJs,
                'input_sync'   => $this->inputSync,
                'filebrowsers' => $this->filebrowsers,
                'base_path'    => $this->basePath,
                'js_path'      => $this->jsPath,
                'jquery_path'  => $this->jqueryPath,
                'config_name'  => $this->configManager->getDefaultConfig(),
                'config'       => array(),
                'plugins'      => array(),
                'styles'       => array(),
                'templates'    => array(),
            ));

        $allowedTypesMap = array(
            'enable'       => 'bool',
            'async'        => 'bool',
            'autoload'     => 'bool',
            'auto_inline'  => 'bool',
            'inline'       => 'bool',
            'jquery'       => 'bool',
            'require_js'   => 'bool',
            'input_sync'   => 'bool',
            'filebrowsers' => 'array',
            'config_name'  => array('string', 'null'),
            'base_path'    => 'string',
            'js_path'      => 'string',
            'jquery_path'  => 'string',
            'config'       => 'array',
            'plugins'      => 'array',
            'styles'       => 'array',
            'templates'    => 'array',
        );

        $normalizers = array(
            'base_path' => function (Options $options, $value) {
                if (substr($value, -1) !== '/') {
                    $value .= '/';
                }

                return $value;
            },
        );

        if (Kernel::VERSION_ID >= 20600) {
            foreach ($allowedTypesMap as $option => $allowedTypes) {
                $resolver->addAllowedTypes($option, $allowedTypes);
            }

            foreach ($normalizers as $option => $normalizer) {
                $resolver->setNormalizer($option, $normalizer);
            }
        } else {
            $resolver
                ->addAllowedTypes($allowedTypesMap)
                ->setNormalizers($normalizers);
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
        return method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')
            ? 'Symfony\Component\Form\Extension\Core\Type\TextareaType'
            : 'textarea';
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
