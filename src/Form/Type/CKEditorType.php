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

        $builder->setAttribute('autoload', $options['autoload']);
        $builder->setAttribute('poweredBy', $options['poweredBy']);
        $builder->setAttribute('resize', $options['resize']);
        $builder->setAttribute('base_path', $options['base_path']);
        $builder->setAttribute('js_path', $options['js_path']);
        $builder->setAttribute('config', $this->resolveConfig($options));
        $builder->setAttribute('config_name', $options['config_name']);
        $builder->setAttribute('plugins', array_merge($this->configuration->getPlugins(), $options['plugins']));
        $builder->setAttribute('styles', array_merge($this->configuration->getStyles(), $options['styles']));
//        $builder->setAttribute('template', array_merge($this->configuration->getTemplate(), $options['template']));
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

        $view->vars['autoload'] = $config->getAttribute('autoload');
        $view->vars['poweredBy'] = $config->getAttribute('poweredBy');
        $view->vars['resize'] = $config->getAttribute('resize');
        $view->vars['base_path'] = $config->getAttribute('base_path');
        $view->vars['js_path'] = $config->getAttribute('js_path');
        $view->vars['config'] = $config->getAttribute('config');
        $view->vars['config_name'] = $config->getAttribute('config_name');
        $view->vars['plugins'] = $config->getAttribute('plugins');
        $view->vars['styles'] = $config->getAttribute('styles');
//        $view->vars['template'] = $config->getAttribute('template');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'enable' => $this->configuration->isEnable(),
                'autoload' => $this->configuration->isAutoload(),
                'poweredBy' => $this->configuration->isPoweredBy(),
                'resize' => $this->configuration->isResize(),
                'base_path' => $this->configuration->getBasePath(),
                'js_path' => $this->configuration->getJsPath(),
                'config_name' => $this->configuration->getDefaultConfig(),
                'config' => [],
                'plugins' => [],
                'styles' => [],
//                'template' => [],
            ])
            ->addAllowedTypes('enable', 'bool')
            ->addAllowedTypes('autoload', 'bool')
            ->addAllowedTypes('poweredBy', 'bool')
            ->addAllowedTypes('resize', 'bool')
            ->addAllowedTypes('config_name', ['string', 'null'])
            ->addAllowedTypes('base_path', 'string')
            ->addAllowedTypes('js_path', 'string')
            ->addAllowedTypes('config', 'array')
            ->addAllowedTypes('config_name', ['string', 'null'])
            ->addAllowedTypes('plugins', 'array')
            ->addAllowedTypes('styles', 'array')
//            ->addAllowedTypes('template', 'array')
            ;
    }

    public function getParent(): string
    {
        // The only editor type which can be initialized on <textarea> elements is the classic editor.
        // This editor hides the passed element and inserts its own UI next to it
        // @link https://ckeditor.com/docs/ckeditor5/latest/support/error-codes.html#error-editor-wrong-element
        return TextareaType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'ckeditor';
    }
}
