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

namespace FOS\CKEditorBundle\DependencyInjection;

use FOS\CKEditorBundle\Exception\DependencyInjectionException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FOSCKEditorExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $this->loadResources($container);
        $container->getDefinition('fos_ck_editor.form.type')
            ->setArgument(5, $config);

        if ($config['enable']) {
            $this->registerConfigs($config, $container);
            $this->registerPlugins($config, $container);
            $this->registerStylesSet($config, $container);
            $this->registerTemplates($config, $container);
            $this->registerToolbars($config, $container);
        }

        if (!method_exists(AbstractType::class, 'getBlockPrefix')) {
            $container->getDefinition('fos_ck_editor.form.type')
                ->clearTag('form.type')
                ->addTag('form.type', ['alias' => 'ckeditor']);
        }

        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['IvoryCKEditorBundle'])) {
            @trigger_error(
                "IvoryCKEditorBundle isn't maintained anymore and should be replaced with FOSCKEditorBundle.",
                E_USER_DEPRECATED
            );
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    private function loadResources(ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $resources = [
            'builder',
            'command',
            'form',
            'installer',
            'renderer',
            'twig',
        ];

        foreach ($resources as $resource) {
            $loader->load($resource.'.xml');
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @throws DependencyInjectionException
     */
    private function registerConfigs(array $config, ContainerBuilder $container)
    {
        if (empty($config['configs'])) {
            return;
        }

        $definition = $container->getDefinition('fos_ck_editor.config_manager');
        $definition->addMethodCall('setConfigs', [$config['configs']]);

        if (!isset($config['default_config']) && !empty($config['configs'])) {
            reset($config['configs']);
            $config['default_config'] = key($config['configs']);
        }

        if (isset($config['default_config'])) {
            if (!isset($config['configs'][$config['default_config']])) {
                throw DependencyInjectionException::invalidDefaultConfig($config['default_config']);
            }

            $definition->addMethodCall('setDefaultConfig', [$config['default_config']]);
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerPlugins(array $config, ContainerBuilder $container)
    {
        if (!empty($config['plugins'])) {
            $container
                ->getDefinition('fos_ck_editor.plugin_manager')
                ->addMethodCall('setPlugins', [$config['plugins']]);
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerStylesSet(array $config, ContainerBuilder $container)
    {
        if (empty($config['styles'])) {
            return;
        }

        $stylesSets = $config['styles'];

        foreach ($stylesSets as &$stylesSet) {
            foreach ($stylesSet as &$value) {
                $value = array_filter($value);
            }
        }

        $container
            ->getDefinition('fos_ck_editor.styles_set_manager')
            ->addMethodCall('setStylesSets', [$stylesSets]);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerTemplates(array $config, ContainerBuilder $container)
    {
        if (!empty($config['templates'])) {
            $container
                ->getDefinition('fos_ck_editor.template_manager')
                ->addMethodCall('setTemplates', [$config['templates']]);
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerToolbars(array $config, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('fos_ck_editor.toolbar_manager');

        if (!empty($config['toolbars']['items'])) {
            $definition->addMethodCall('setItems', [$config['toolbars']['items']]);
        }

        if (!empty($config['toolbars']['configs'])) {
            $definition->addMethodCall('setToolbars', [$config['toolbars']['configs']]);
        }
    }

    public function getAlias()
    {
        return 'fos_ck_editor';
    }
}
