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

use Ivory\CKEditorBundle\Exception\DependencyInjectionException,
    Symfony\Component\Config\Definition\Processor,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
    Symfony\Component\HttpKernel\DependencyInjection\Extension;

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
            $container->setParameter('templating.helper.form.resources', array_merge(
                $container->hasParameter('templating.helper.form.resources')
                    ? $container->getParameter('templating.helper.form.resources')
                    : array(),
                array('IvoryCKEditorBundle:Form')
            ));
        }

        if (in_array('twig', $templatingEngines)) {
            $container->setParameter('twig.form.resources', array_merge(
                $container->hasParameter('twig.form.resources')
                    ? $container->getParameter('twig.form.resources')
                    : array(),
                array('IvoryCKEditorBundle:Form:ckeditor_widget.html.twig')
            ));
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
     * Merges the toolbars into the CKEditor configurations.
     *
     * @param array $config The CKEditor configuration.
     *
     * @return array The CKEditor configuration with merged toolbars.
     */
    protected function mergeToolbars(array $config)
    {
        $toolbars = array();
        foreach ($config['toolbars']['configs'] as $name => $toolbar) {
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
}
