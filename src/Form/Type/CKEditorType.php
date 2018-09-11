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
     * @var array
     */
    private $config;

    public function __construct(
        ConfigManagerInterface $configManager,
        PluginManagerInterface $pluginManager,
        StylesSetManagerInterface $stylesSetManager,
        TemplateManagerInterface $templateManager,
        ToolbarManagerInterface $toolbarManager,
        array $config
    ) {
        $this->configManager = $configManager;
        $this->pluginManager = $pluginManager;
        $this->stylesSetManager = $stylesSetManager;
        $this->templateManager = $templateManager;
        $this->toolbarManager = $toolbarManager;
        $this->config = $config;
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
                'enable' => $this->config['enable'],
                'async' => $this->config['async'],
                'autoload' => $this->config['autoload'],
                'auto_inline' => $this->config['auto_inline'],
                'inline' => $this->config['inline'],
                'jquery' => $this->config['jquery'],
                'require_js' => $this->config['require_js'],
                'input_sync' => $this->config['input_sync'],
                'filebrowsers' => $this->config['filebrowsers'],
                'base_path' => $this->config['base_path'],
                'js_path' => $this->config['js_path'],
                'jquery_path' => $this->config['jquery_path'],
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
