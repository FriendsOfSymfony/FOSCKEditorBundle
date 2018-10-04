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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
final class FOSCKEditorExtension extends ConfigurableExtension
{
    protected function loadInternal(array $config, ContainerBuilder $container): void
    {
        $this->loadResources($container);

        $container->getDefinition('fos_ck_editor.configuration')
            ->setArgument(0, $config);

        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['IvoryCKEditorBundle'])) {
            @trigger_error(
                "IvoryCKEditorBundle isn't maintained anymore and should be replaced with FOSCKEditorBundle.",
                E_USER_DEPRECATED
            );
        }
    }

    private function loadResources(ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $resources = [
            'builder',
            'command',
            'config',
            'form',
            'installer',
            'renderer',
            'twig',
        ];

        foreach ($resources as $resource) {
            $loader->load($resource.'.xml');
        }
    }

    public function getAlias(): string
    {
        return 'fos_ck_editor';
    }
}
