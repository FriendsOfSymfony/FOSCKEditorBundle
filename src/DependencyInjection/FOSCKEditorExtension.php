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

        if ($config['enable']) {
            $config = $this->resolveConfigs($config, $container);
            $config = $this->resolveStylesSet($config, $container);
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
