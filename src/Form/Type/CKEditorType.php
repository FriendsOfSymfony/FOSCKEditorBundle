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

use FOS\CKEditorBundle\Config\CKEditorConfigurationInterface;
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
final class CKEditorType extends AbstractType
{
    /**
     * @var CKEditorConfigurationInterface
     */
    private $configuration;

    public function __construct(CKEditorConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
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
        $builder->setAttribute('config', $this->resolveConfig($options));
        $builder->setAttribute('config_name', $options['config_name']);
        $builder->setAttribute('plugins', array_merge($this->configuration->getPlugins(), $options['plugins']));
        $builder->setAttribute('styles', array_merge($this->configuration->getStyles(), $options['styles']));
        $builder->setAttribute('templates', array_merge($this->configuration->getTemplates(), $options['templates']));
    }

    private function resolveConfig(array $options): array
    {
        $config = $options['config'];

        if (null === $options['config_name']) {
            $options['config_name'] = uniqid('fos', true);
        } else {
            $config = array_merge($this->configuration->getConfig($options['config_name']), $config);
        }

        if (isset($config['toolbar']) && is_string($config['toolbar'])) {
            $config['toolbar'] = $this->configuration->getToolbar($config['toolbar']);
        }

        return $config;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
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
        $view->vars['config_name'] = $config->getAttribute('config_name');
        $view->vars['plugins'] = $config->getAttribute('plugins');
        $view->vars['styles'] = $config->getAttribute('styles');
        $view->vars['templates'] = $config->getAttribute('templates');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'enable' => $this->configuration->isEnable(),
                'async' => $this->configuration->isAsync(),
                'autoload' => $this->configuration->isAutoload(),
                'auto_inline' => $this->configuration->isAutoInline(),
                'inline' => $this->configuration->isInline(),
                'jquery' => $this->configuration->isJquery(),
                'require_js' => $this->configuration->isRequireJs(),
                'input_sync' => $this->configuration->isInputSync(),
                'filebrowsers' => $this->configuration->getFilebrowsers(),
                'base_path' => $this->configuration->getBasePath(),
                'js_path' => $this->configuration->getJsPath(),
                'jquery_path' => $this->configuration->getJqueryPath(),
                'config_name' => $this->configuration->getDefaultConfig(),
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

    public function getParent(): string
    {
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'ckeditor';
    }
}
