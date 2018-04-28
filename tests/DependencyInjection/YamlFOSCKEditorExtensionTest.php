<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\CKEditorBundle\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class YamlFOSCKEditorExtensionTest extends AbstractFOSCKEditorExtensionTest
{
    /**
     * {@inheritdoc}
     */
    protected function loadConfiguration(ContainerBuilder $container, $configuration)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Fixtures/config/Yaml/'));
        $loader->load($configuration.'.yml');
    }
}
