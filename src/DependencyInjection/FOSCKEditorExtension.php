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
    private const DEFAULT_TOOLBAR_ITEMS = [
        'basic.about' => ['About'],
        'basic.basic_styles' => ['Bold', 'Italic'],
        'basic.links' => ['Link', 'Unlink'],
        'basic.paragraph' => ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'],
        'standard.about' => ['Styles', 'Format', 'About'],
        'standard.basic_styles' => ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat'],
        'standard.clipboard' => ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
        'standard.document' => ['Source'],
        'standard.editing' => ['Scayt'],
        'standard.links' => ['Link', 'Unlink', 'Anchor'],
        'standard.insert' => ['Image', 'Table', 'HorizontalRule', 'SpecialChar'],
        'standard.paragraph' => ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'],
        'standard.tools' => ['Maximize'],
        'full.about' => ['About'],
        'full.basic_styles' => [
            'Bold',
            'Italic',
            'Underline',
            'Strike',
            'Subscript',
            'Superscript',
            '-',
            'RemoveFormat',
        ],
        'full.clipboard' => ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
        'full.colors' => ['TextColor', 'BGColor'],
        'full.document' => ['Source', '-', 'NewPage', 'Preview', 'Print', '-', 'Templates'],
        'full.editing' => ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'],
        'full.forms' => [
            'Form',
            'Checkbox',
            'Radio',
            'TextField',
            'Textarea',
            'SelectField',
            'Button',
            'ImageButton',
            'HiddenField',
        ],
        'full.insert' => ['Image', 'Flash', 'Table', 'HorizontalRule', 'SpecialChar', 'Smiley', 'PageBreak', 'Iframe'],
        'full.links' => ['Link', 'Unlink', 'Anchor'],
        'full.paragraph' => [
            'NumberedList',
            'BulletedList',
            '-',
            'Outdent',
            'Indent',
            '-',
            'Blockquote',
            'CreateDiv',
            '-',
            'JustifyLeft',
            'JustifyCenter',
            'JustifyRight',
            'JustifyBlock',
            '-',
            'BidiLtr',
            'BidiRtl',
        ],
        'full.styles' => ['Styles', 'Format', 'Font', 'FontSize', 'TextColor', 'BGColor'],
        'full.tools' => ['Maximize', 'ShowBlocks'],
    ];

    private const DEFAULT_TOOLBAR_CONFIGS = [
        'basic' => [
            '@basic.basic_styles',
            '@basic.paragraph',
            '@basic.links',
            '@basic.about',
        ],
        'standard' => [
            '@standard.clipboard',
            '@standard.editing',
            '@standard.links',
            '@standard.insert',
            '@standard.tools',
            '@standard.document',
            '/',
            '@standard.basic_styles',
            '@standard.paragraph',
            '@standard.about',
        ],
        'full' => [
            '@full.document',
            '@full.clipboard',
            '@full.editing',
            '@full.forms',
            '/',
            '@full.basic_styles',
            '@full.paragraph',
            '@full.links',
            '@full.insert',
            '/',
            '@full.styles',
            '@full.colors',
            '@full.tools',
            '@full.about',
        ],
    ];

    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $this->loadResources($container);

        $config = $this->addDefaultToolbars($config);

        if ($config['enable']) {
            $config = $this->resolveConfigs($config);
            $config = $this->resolveStylesSet($config);
        }

        $container->getDefinition('fos_ck_editor.form.type')
            ->setArgument(0, $config);

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

    private function addDefaultToolbars(array $config)
    {
        $config['toolbars']['items'] = array_merge(self::DEFAULT_TOOLBAR_ITEMS, $config['toolbars']['items']);
        $config['toolbars']['configs'] = array_merge(self::DEFAULT_TOOLBAR_CONFIGS, $config['toolbars']['configs']);

        return $config;
    }

    /**
     * @throws DependencyInjectionException
     */
    private function resolveConfigs(array $config): array
    {
        if (empty($config['configs'])) {
            return $config;
        }

        if (!isset($config['default_config']) && !empty($config['configs'])) {
            reset($config['configs']);
            $config['default_config'] = key($config['configs']);
        }

        if (isset($config['default_config']) && !isset($config['configs'][$config['default_config']])) {
            throw DependencyInjectionException::invalidDefaultConfig($config['default_config']);
        }

        return $config;
    }

    private function resolveStylesSet(array $config)
    {
        if (empty($config['styles'])) {
            return $config;
        }

        $stylesSets = $config['styles'];

        foreach ($stylesSets as &$stylesSet) {
            foreach ($stylesSet as &$value) {
                $value = array_filter($value);
            }
        }

        return $config;
    }

    public function getAlias()
    {
        return 'fos_ck_editor';
    }
}
