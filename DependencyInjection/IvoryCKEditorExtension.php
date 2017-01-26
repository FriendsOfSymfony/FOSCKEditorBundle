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
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class IvoryCKEditorExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        foreach (array('form', 'renderer', 'templating', 'twig') as $service) {
            $loader->load($service.'.xml');
        }

        $this->registerConfig($config, $container);

        if (!isset($config['enable']) || $config['enable']) {
            $this->registerConfigs($config, $container);
            $this->registerPlugins($config, $container);
            $this->registerStylesSet($config, $container);
            $this->registerTemplates($config, $container);
            $this->registerToolbars($config, $container);
            $this->registerFilebrowsers($config, $container);
        }

        if (Kernel::VERSION_ID < 30000) {
            $container->getDefinition('ivory_ck_editor.form.type')
                ->clearTag('form.type')
                ->addTag('form.type', array('alias' => 'ckeditor'));
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerConfig(array $config, ContainerBuilder $container)
    {
        $formType = $container->getDefinition('ivory_ck_editor.form.type');

        if (isset($config['enable'])) {
            $formType->addMethodCall('isEnable', array($config['enable']));
        }

        if (isset($config['async'])) {
            $formType->addMethodCall('isAsync', array($config['async']));
        }

        if (isset($config['auto_inline'])) {
            $formType->addMethodCall('isAutoInline', array($config['auto_inline']));
        }

        if (isset($config['inline'])) {
            $formType->addMethodCall('isInline', array($config['inline']));
        }

        if (isset($config['autoload'])) {
            $formType->addMethodCall('isAutoload', array($config['autoload']));
        }

        if (isset($config['jquery'])) {
            $formType->addMethodCall('useJquery', array($config['jquery']));
        }

        if (isset($config['require_js'])) {
            $formType->addMethodCall('useRequireJs', array($config['require_js']));
        }

        if (isset($config['input_sync'])) {
            $formType->addMethodCall('isInputSync', array($config['input_sync']));
        }

        if (isset($config['base_path'])) {
            $formType->addMethodCall('setBasePath', array($config['base_path']));
        }

        if (isset($config['js_path'])) {
            $formType->addMethodCall('setJsPath', array($config['js_path']));
        }

        if (isset($config['jquery_path'])) {
            $formType->addMethodCall('setJqueryPath', array($config['jquery_path']));
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

        $definition = $container->getDefinition('ivory_ck_editor.config_manager');
        $definition->addMethodCall('setConfigs', array($config['configs']));

        if (!isset($config['default_config']) && !empty($config['configs'])) {
            reset($config['configs']);
            $config['default_config'] = key($config['configs']);
        }

        if (isset($config['default_config'])) {
            if (!isset($config['configs'][$config['default_config']])) {
                throw DependencyInjectionException::invalidDefaultConfig($config['default_config']);
            }

            $definition->addMethodCall('setDefaultConfig', array($config['default_config']));
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
                ->getDefinition('ivory_ck_editor.plugin_manager')
                ->addMethodCall('setPlugins', array($config['plugins']));
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
            ->getDefinition('ivory_ck_editor.styles_set_manager')
            ->addMethodCall('setStylesSets', array($stylesSets));
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerTemplates(array $config, ContainerBuilder $container)
    {
        if (!empty($config['templates'])) {
            $container
                ->getDefinition('ivory_ck_editor.template_manager')
                ->addMethodCall('setTemplates', array($config['templates']));
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerToolbars(array $config, ContainerBuilder $container)
    {
        $definition = $container->getDefinition('ivory_ck_editor.toolbar_manager');

        if (!empty($config['toolbars']['items'])) {
            $definition->addMethodCall('setItems', array($config['toolbars']['items']));
        }

        if (!empty($config['toolbars']['configs'])) {
            $definition->addMethodCall('setToolbars', array($config['toolbars']['configs']));
        }
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerFilebrowsers(array $config, ContainerBuilder $container)
    {
        if (!empty($config['filebrowsers'])) {
            $container
                ->getDefinition('ivory_ck_editor.form.type')
                ->addMethodCall('setFilebrowsers', array($config['filebrowsers']));
        }
    }
}
