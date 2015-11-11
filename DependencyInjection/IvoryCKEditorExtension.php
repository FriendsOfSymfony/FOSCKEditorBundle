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

/**
 * Ivory CKEditor extension.
 *
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
        foreach (array('form', 'templating', 'twig') as $service) {
            $loader->load($service.'.xml');
        }

        $this->registerConfig($config, $container);

        if (!isset($config['enable']) || $config['enable']) {
            $this->registerConfigs($config, $container);
            $this->registerPlugins($config, $container);
            $this->registerStylesSet($config, $container);
            $this->registerTemplates($config, $container);
        }
    }

    /**
     * Registers the CKEditor config.
     *
     * @param array                                                   $config    The CKEditor configuration
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container The container.
     */
    private function registerConfig(array $config, ContainerBuilder $container)
    {
        $formType = $container->getDefinition('ivory_ck_editor.form.type');

        if (isset($config['enable'])) {
            $formType->addMethodCall('isEnable', array($config['enable']));
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
     * Registers the CKEditor configs.
     *
     * @param array                                                   $config    The CKEditor configuration.
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container The container.
     *
     * @throws \Ivory\CKEditorBundle\Exception\DependencyInjectionException If the default config does not exist.
     */
    private function registerConfigs(array $config, ContainerBuilder $container)
    {
        if (empty($config['configs'])) {
            return;
        }

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
    private function registerPlugins(array $config, ContainerBuilder $container)
    {
        if (empty($config['plugins'])) {
            return;
        }

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
    private function registerStylesSet(array $config, ContainerBuilder $container)
    {
        if (empty($config['styles'])) {
            return;
        }

        $definition = $container->getDefinition('ivory_ck_editor.styles_set_manager');

        foreach ($config['styles'] as $name => $stylesSet) {
            $definition->addMethodCall('setStylesSet', array($name, $this->fixStylesSet($stylesSet)));
        }
    }

    /**
     * Registers the CKEditor templates.
     *
     * @param array                                                   $config    The CKEditor configuration.
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container The container.
     */
    private function registerTemplates(array $config, ContainerBuilder $container)
    {
        if (empty($config['templates'])) {
            return;
        }

        $definition = $container->getDefinition('ivory_ck_editor.template_manager');

        foreach ($config['templates'] as $name => $template) {
            $definition->addMethodCall('setTemplate', array($name, $template));
        }
    }

    /**
     * Merges the toolbars into the CKEditor configs.
     *
     * @param array $config The CKEditor configuration.
     *
     * @throws \Ivory\CKEditorBundle\Exception\DependencyInjectionException If a toolbar does not exist.
     *
     * @return array The CKEditor configuration with merged toolbars.
     */
    private function mergeToolbars(array $config)
    {
        $resolvedToolbars = $this->resolveToolbars($config);
        unset($config['toolbars']);

        foreach ($config['configs'] as $name => $configuration) {
            if (!isset($configuration['toolbar']) || !is_string($configuration['toolbar'])) {
                continue;
            }

            if (!isset($resolvedToolbars[$configuration['toolbar']])) {
                throw DependencyInjectionException::invalidToolbar($configuration['toolbar']);
            }

            $config['configs'][$name]['toolbar'] = $resolvedToolbars[$configuration['toolbar']];
        }

        return $config;
    }

    /**
     * Resolves the CKEditor toolbars.
     *
     * @param array $config The CKEditor configuration.
     *
     * @return array The resolved CKEditor toolbars.
     */
    private function resolveToolbars(array $config)
    {
        $resolvedToolbars = array();

        foreach ($config['toolbars']['configs'] as $name => $toolbar) {
            $resolvedToolbars[$name] = array();

            foreach ($toolbar as $item) {
                $resolvedToolbars[$name][] = $this->resolveToolbarItem($item, $config['toolbars']['items']);
            }
        }

        return array_merge($this->getDefaultToolbars(), $resolvedToolbars);
    }

    /**
     * Resolves a CKEditor toolbar item.
     *
     * @param string|array $item  The CKEditor item.
     * @param array        $items The CKEditor items.
     *
     * @throws \Ivory\CKEditorBundle\Exception\DependencyInjectionException If the toolbar item does not exist.
     *
     * @return array|string The resolved CKEditor toolbar item.
     */
    private function resolveToolbarItem($item, array $items)
    {
        if (is_string($item) && ($item[0] === '@')) {
            $itemName = substr($item, 1);

            if (!isset($items[$itemName])) {
                throw DependencyInjectionException::invalidToolbarItem($itemName);
            }

            return $items[$itemName];
        }

        return $item;
    }

    /**
     * Fixes the CKEditor styles set.
     *
     * @param array $stylesSet The CKEditor styles set.
     *
     * @return array The fixed CKEditor styles set.
     */
    private function fixStylesSet(array $stylesSet)
    {
        foreach ($stylesSet as &$value) {
            $value = array_filter($value);
        }

        return $stylesSet;
    }

    /**
     * Gets the default CKEditor toolbars.
     *
     * @return array The default CKEditor toolbars.
     */
    private function getDefaultToolbars()
    {
        return array(
            'full'     => $this->getFullToolbar(),
            'standard' => $this->getStandardToolbar(),
            'basic'    => $this->getBasicToolbar(),
        );
    }

    /**
     * Gets the full CKEditor toolbar.
     *
     * @return array The full CKEditor toolbar.
     */
    private function getFullToolbar()
    {
        return array(
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
        );
    }

    /**
     * Gets the standard CKEditor toolbar.
     *
     * @return array The standard CKEditor toolbar.
     */
    private function getStandardToolbar()
    {
        return array(
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
        );
    }

    /**
     * Gets the basic CKEditor toolbar.
     *
     * @return array The basic CKEditor toolbar.
     */
    private function getBasicToolbar()
    {
        return array(
            array('Bold', 'Italic'),
            array('NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'),
            array('Link', 'Unlink'),
            array('About'),
        );
    }
}
