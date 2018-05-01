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

namespace FOS\CKEditorBundle\Form\Type;

use FOS\CKEditorBundle\Model\ConfigManagerInterface;
use FOS\CKEditorBundle\Model\PluginManagerInterface;
use FOS\CKEditorBundle\Model\StylesSetManagerInterface;
use FOS\CKEditorBundle\Model\TemplateManagerInterface;
use FOS\CKEditorBundle\Model\ToolbarManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
    private $filebrowsers = [];

    /**
     * @var string
     */
    private $basePath = 'bundles/fosckeditor/';

    /**
     * @var string
     */
    private $jsPath = 'bundles/fosckeditor/ckeditor.js';

    /**
     * @var string
     */
    private $jqueryPath = 'bundles/fosckeditor/adapters/jquery.js';

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
        if (null !== $enable) {
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
        if (null !== $async) {
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
        if (null !== $autoload) {
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
        if (null !== $autoInline) {
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
        if (null !== $inline) {
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
        if (null !== $jquery) {
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
        if (null !== $requireJs) {
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
        if (null !== $inputSync) {
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

        if (!$options['enable']) {
            return;
        }

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

        if (null === $options['config_name']) {
            $options['config_name'] = uniqid('fos', true);
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

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $config = $form->getConfig();
        $view->vars['enable'] = $config->getAttribute('enable');

        if (!$view->vars['enable']) {
            return;
        }

        $view->vars['async'] = $config->getAttribute('async');
        $view->vars['autoload'] = $config->getAttribute('autoload');
        $view->vars['auto_inline'] = $config->getAttribute('auto_inline');
        $view->vars['inline'] = $config->getAttribute('inline');
        $view->vars['jquery'] = $config->getAttribute('jquery');
        $view->vars['require_js'] = $config->getAttribute('require_js');
        $view->vars['input_sync'] = $config->getAttribute('input_sync');
        $view->vars['filebrowsers'] = $config->getAttribute('filebrowsers');
        $view->vars['base_path'] = $config->getAttribute('base_path');
        $view->vars['js_path'] = $config->getAttribute('js_path');
        $view->vars['jquery_path'] = $config->getAttribute('jquery_path');
        $view->vars['config'] = $config->getAttribute('config');
        $view->vars['plugins'] = $config->getAttribute('plugins');
        $view->vars['styles'] = $config->getAttribute('styles');
        $view->vars['templates'] = $config->getAttribute('templates');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'enable' => $this->enable,
                'async' => $this->async,
                'autoload' => $this->autoload,
                'auto_inline' => $this->autoInline,
                'inline' => $this->inline,
                'jquery' => $this->jquery,
                'require_js' => $this->requireJs,
                'input_sync' => $this->inputSync,
                'filebrowsers' => $this->filebrowsers,
                'base_path' => $this->basePath,
                'js_path' => $this->jsPath,
                'jquery_path' => $this->jqueryPath,
                'config_name' => $this->configManager->getDefaultConfig(),
                'config' => [],
                'plugins' => [],
                'styles' => [],
                'templates' => [],
            ])
            ->addAllowedTypes('enable', 'bool')
            ->addAllowedTypes('async', 'bool')
            ->addAllowedTypes('autoload', 'bool')
            ->addAllowedTypes('auto_inline', 'bool')
            ->addAllowedTypes('inline', 'bool')
            ->addAllowedTypes('jquery', 'bool')
            ->addAllowedTypes('require_js', 'bool')
            ->addAllowedTypes('input_sync', 'bool')
            ->addAllowedTypes('filebrowsers', 'array')
            ->addAllowedTypes('config_name', ['string', 'null'])
            ->addAllowedTypes('base_path', 'string')
            ->addAllowedTypes('js_path', 'string')
            ->addAllowedTypes('jquery_path', 'string')
            ->addAllowedTypes('config', 'array')
            ->addAllowedTypes('plugins', 'array')
            ->addAllowedTypes('styles', 'array')
            ->addAllowedTypes('templates', 'array')
            ->setNormalizer('base_path', function (Options $options, $value) {
                if ('/' !== substr($value, -1)) {
                    $value .= '/';
                }

                return $value;
            });
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return method_exists(AbstractType::class, 'getBlockPrefix') ? TextareaType::class : 'textarea';
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
