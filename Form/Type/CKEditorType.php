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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorType extends AbstractType
{
    /**
     * @var bool
     */
    private $enable = true;

    /**
     * @var bool
     */
    private $async = false;

    /**
     * @var bool
     */
    private $autoload = true;

    /**
     * @var bool
     */
    private $autoInline = true;

    /**
     * @var bool
     */
    private $inline = false;

    /**
     * @var bool
     */
    private $jquery = false;

    /**
     * @var bool
     */
    private $requireJs = false;

    /**
     * @var bool
     */
    private $inputSync = false;

    /**
     * @var array
     */
    private $filebrowsers = array();

    /**
     * @var string
     */
    private $basePath = 'bundles/ivoryckeditor/';

    /**
     * @var string
     */
    private $jsPath = 'bundles/ivoryckeditor/ckeditor.js';

    /**
     * @var string
     */
    private $jqueryPath = 'bundles/ivoryckeditor/adapters/jquery.js';

    /**
     * @var ConfigManagerInterface
     */
    private $configManager;

    /**
     * @var PluginManagerInterface
     */
    private $pluginManager;

    /**
     * @var StylesSetManagerInterface
     */
    private $stylesSetManager;

    /**
     * @var TemplateManagerInterface
     */
    private $templateManager;

    /**
     * @var ToolbarManagerInterface
     */
    private $toolbarManager;

    /**
     * @param ConfigManagerInterface    $configManager
     * @param PluginManagerInterface    $pluginManager
     * @param StylesSetManagerInterface $stylesSetManager
     * @param TemplateManagerInterface  $templateManager
     * @param ToolbarManagerInterface   $toolbarManager
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
     * @param bool|null $enable
     *
     * @return bool
     */
    public function isEnable($enable = null)
    {
        if ($enable !== null) {
            $this->enable = (bool) $enable;
        }

        return $this->enable;
    }

    /**
     * @param bool|null $async
     *
     * @return bool
     */
    public function isAsync($async = null)
    {
        if ($async !== null) {
            $this->async = (bool) $async;
        }

        return $this->async;
    }

    /**
     * @param bool $autoload
     *
     * @return bool
     */
    public function isAutoload($autoload = null)
    {
        if ($autoload !== null) {
            $this->autoload = (bool) $autoload;
        }

        return $this->autoload;
    }

    /**
     * @param bool $autoInline
     *
     * @return bool
     */
    public function isAutoInline($autoInline = null)
    {
        if ($autoInline !== null) {
            $this->autoInline = (bool) $autoInline;
        }

        return $this->autoInline;
    }

    /**
     * @param bool $inline
     *
     * @return bool
     */
    public function isInline($inline = null)
    {
        if ($inline !== null) {
            $this->inline = (bool) $inline;
        }

        return $this->inline;
    }

    /**
     * @param bool $jquery
     *
     * @return bool
     */
    public function useJquery($jquery = null)
    {
        if ($jquery !== null) {
            $this->jquery = (bool) $jquery;
        }

        return $this->jquery;
    }

    /**
     * @param bool $requireJs
     *
     * @return bool
     */
    public function useRequireJs($requireJs = null)
    {
        if ($requireJs !== null) {
            $this->requireJs = (bool) $requireJs;
        }

        return $this->requireJs;
    }

    /**
     * @param bool $inputSync
     *
     * @return bool
     */
    public function isInputSync($inputSync = null)
    {
        if ($inputSync !== null) {
            $this->inputSync = (bool) $inputSync;
        }

        return $this->inputSync;
    }

    /**
     * @return bool
     */
    public function hasFilebrowsers()
    {
        return !empty($this->filebrowsers);
    }

    /**
     * @return array
     */
    public function getFilebrowsers()
    {
        return $this->filebrowsers;
    }

    /**
     * @param array $filebrowsers
     */
    public function setFilebrowsers(array $filebrowsers)
    {
        foreach ($filebrowsers as $filebrowser) {
            $this->addFilebrowser($filebrowser);
        }
    }

    /**
     * @param string $filebrowser
     *
     * @return bool
     */
    public function hasFilebrowser($filebrowser)
    {
        return in_array($filebrowser, $this->filebrowsers, true);
    }

    /**
     * @param string $filebrowser
     */
    public function addFilebrowser($filebrowser)
    {
        if (!$this->hasFilebrowser($filebrowser)) {
            $this->filebrowsers[] = $filebrowser;
        }
    }

    /**
     * @param string $filebrowser
     */
    public function removeFilebrowser($filebrowser)
    {
        unset($this->filebrowsers[array_search($filebrowser, $this->filebrowsers, true)]);
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return string
     */
    public function getJsPath()
    {
        return $this->jsPath;
    }

    /**
     * @param string $jsPath
     */
    public function setJsPath($jsPath)
    {
        $this->jsPath = $jsPath;
    }

    /**
     * @return string
     */
    public function getJqueryPath()
    {
        return $this->jqueryPath;
    }

    /**
     * @param string $jqueryPath
     */
    public function setJqueryPath($jqueryPath)
    {
        $this->jqueryPath = $jqueryPath;
    }

    /**
     * @return ConfigManagerInterface
     */
    public function getConfigManager()
    {
        return $this->configManager;
    }

    /**
     * @param ConfigManagerInterface $configManager
     */
    public function setConfigManager(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @return PluginManagerInterface
     */
    public function getPluginManager()
    {
        return $this->pluginManager;
    }

    /**
     * @param PluginManagerInterface $pluginManager
     */
    public function setPluginManager(PluginManagerInterface $pluginManager)
    {
        $this->pluginManager = $pluginManager;
    }

    /**
     * @return StylesSetManagerInterface
     */
    public function getStylesSetManager()
    {
        return $this->stylesSetManager;
    }

    /**
     * @param StylesSetManagerInterface $stylesSetManager
     */
    public function setStylesSetManager(StylesSetManagerInterface $stylesSetManager)
    {
        $this->stylesSetManager = $stylesSetManager;
    }

    /**
     * @return TemplateManagerInterface
     */
    public function getTemplateManager()
    {
        return $this->templateManager;
    }

    /**
     * @param TemplateManagerInterface $templateManager
     */
    public function setTemplateManager(TemplateManagerInterface $templateManager)
    {
        $this->templateManager = $templateManager;
    }

    /**
     * @return ToolbarManagerInterface
     */
    public function getToolbarManager()
    {
        return $this->toolbarManager;
    }

    /**
     * @param ToolbarManagerInterface $toolbarManager
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
