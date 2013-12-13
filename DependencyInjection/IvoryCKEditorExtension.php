<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\DependencyInjection;

use Ivory\CKEditorBundle\Exception\DependencyInjectionException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Ivory CKEditor extension.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class IvoryCKEditorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $configs);

        $this->register($config, $container);
    }

    /**
     * Registers the CKEditor configuration.
     *
     * @param array                                                   $config    The CKEditor configuration
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container The container.
     */
    protected function register(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        foreach (array('helper', 'form') as $service) {
            $loader->load($service.'.xml');
        }

        $this->registerResources($container);

        $container->setParameter('ivory_ck_editor.form.type.enable', $config['enable']);
        $container->setParameter('ivory_ck_editor.form.type.base_path', $config['base_path']);
        $container->setParameter('ivory_ck_editor.form.type.js_path', $config['js_path']);

        if ($config['enable']) {
            if (!empty($config['configs'])) {
                $this->registerConfigs($config, $container);
            }

            if (!empty($config['plugins'])) {
                $this->registerPlugins($config, $container);
            }

            if (!empty($config['styles'])) {
                $this->registerStylesSet($config, $container);
            }

            if (!empty($config['templates'])) {
                $this->registerTemplates($config, $container);
            }
        }
    }

    /**
     * Registers the form resources for the PHP & Twig templating engines.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container The container.
     */
    protected function registerResources(ContainerBuilder $container)
    {
        $templatingEngines = $container->getParameter('templating.engines');

        if (in_array('php', $templatingEngines)) {
            $phpFormResources = $container->hasParameter('templating.helper.form.resources')
                ? $container->getParameter('templating.helper.form.resources')
                : array();

            $container->setParameter(
                'templating.helper.form.resources',
                array_merge($phpFormResources, array('IvoryCKEditorBundle:Form'))
            );
        }

        if (in_array('twig', $templatingEngines)) {
            $twigFormResources = $container->hasParameter('twig.form.resources')
                ? $container->getParameter('twig.form.resources')
                : array();

            $container->setParameter(
                'twig.form.resources',
                array_merge($twigFormResources, array('IvoryCKEditorBundle:Form:ckeditor_widget.html.twig'))
            );
        }
    }

    /**
     * Register the CKEditor configurations.
     *
     * @param array                                                   $config    The CKEditor configuration.
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container The container.
     */
    protected function registerConfigs(array $config, ContainerBuilder $container)
    {
        $config = $this->mergeToolbars($config);

        $definition = $container->getDefinition('ivory_ck_editor.config_manager');
        foreach ($config['configs'] as $name => $configuration) {
            $definition->addMethodCall('setConfig', array($name, $configuration));
        }

        if (isset($config['default_config'])) {
            if (!isset($config['configs'][$config['default_config']])) {
                throw DependencyInjectionException::invalidDefaultConfig($config['default_config']);
            }

            $definition->addMethodCall('setDefaultConfig', array($config['default_config']));
        }
    }

    /**
     * Registers the CKEditor plugins.
     *
     * @param array                                                   $config    The CKEditor configuration.
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container The container.
     */
    protected function registerPlugins(array $config, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('ivory_ck_editor.plugin_manager');

        foreach ($config['plugins'] as $name => $plugin) {
            $definition->addMethodCall('setPlugin', array($name, $plugin));
        }
    }

    /**
     * Registers the CKEditor styles set.
     *
     * @param array                                                   $config    The CKEditor configuration.
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container The container.
     */
    protected function registerStylesSet(array $config, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('ivory_ck_editor.styles_set_manager');

        foreach ($config['styles'] as $name => $styleSet) {
            foreach ($styleSet as &$style) {
                if (empty($style['styles'])) {
                    unset($style['styles']);
                }

                if (empty($style['attributes'])) {
                    unset($style['attributes']);
                }
            }

            $definition->addMethodCall('setStylesSet', array($name, $styleSet));
        }
    }

    /**
     * Registers the CKEditor templates.
     *
     * @param array                                                   $config    The CKEditor configuration.
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container The container.
     */
    protected function registerTemplates(array $config, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('ivory_ck_editor.template_manager');

        foreach ($config['templates'] as $name => $template) {
            $definition->addMethodCall('setTemplate', array($name, $template));
        }
    }

    /**
     * Merges the toolbars into the CKEditor configurations.
     *
     * @param array $config The CKEditor configuration.
     *
     * @return array The CKEditor configuration with merged toolbars.
     */
    protected function mergeToolbars(array $config)
    {
        $toolbars = array();
        $toolbarConfigs = array_merge($this->getDefaultToolbars(), $config['toolbars']['configs']);

        foreach ($toolbarConfigs as $name => $toolbar) {
            $toolbars[$name] = array();

            foreach ($toolbar as $item) {
                if (is_string($item) && ($item[0] === '@')) {
                    $itemName = substr($item, 1);

                    if (!isset($config['toolbars']['items'][$itemName])) {
                        throw DependencyInjectionException::invalidToolbarItem($itemName);
                    }

                    $item = $config['toolbars']['items'][$itemName];
                }

                $toolbars[$name][] = $item;
            }
        }

        foreach ($config['configs'] as $name => $configuration) {
            if (isset($configuration['toolbar']) && is_string($configuration['toolbar'])) {
                if (!isset($toolbars[$configuration['toolbar']])) {
                    throw DependencyInjectionException::invalidToolbar($configuration['toolbar']);
                }

                $config['configs'][$name]['toolbar'] = $toolbars[$configuration['toolbar']];
            }
        }

        unset($config['toolbars']);

        return $config;
    }

    /**
     * Gets the default toolbars.
     *
     * @return array The default toolbars.
     */
    protected function getDefaultToolbars()
    {
        return array(
            'full' => array(
                array('Source', '-', 'NewPage', 'Preview', 'Print', '-', 'Templates'),
                array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
                array('Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'),
                array(
                    'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'SelectField', 'Button', 'ImageButton',
                    'HiddenField',
                ),
                '/',
                array('Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'),
                array(
                    'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-',
                    'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl',
                ),
                array('Link', 'Unlink', 'Anchor'),
                array('Image', 'FLash', 'Table', 'HorizontalRule', 'SpecialChar', 'Smiley', 'PageBreak', 'Iframe'),
                '/',
                array('Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor'),
                array('Maximize', 'ShowBlocks'),
                array('About'),
            ),
            'standard' => array(
                array('Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'),
                array('Scayt'),
                array('Link', 'Unlink', 'Anchor'),
                array('Image', 'Table', 'HorizontalRule', 'SpecialChar'),
                array('Maximize'),
                array('Source'),
                '/',
                array('Bold', 'Italic', 'Strike', '-', 'RemoveFormat'),
                array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'),
                array('Styles', 'Format', 'About'),
            ),
            'basic' => array(
                array('Bold', 'Italic'),
                array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'),
                array('Link', 'Unlink'),
                array('About'),
            ),
        );
    }
}
