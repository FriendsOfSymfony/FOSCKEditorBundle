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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class FOSCKEditorExtension extends ConfigurableExtension
{
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $this->loadResources($container);

        $container->getDefinition('fos_ck_editor.configuration')
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

    public function getAlias()
    {
        return 'fos_ck_editor';
    }
}
