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

namespace FOS\CKEditorBundle\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class YamlFOSCKEditorExtensionTest extends AbstractFOSCKEditorExtensionTest
{
    protected function loadConfiguration(ContainerBuilder $container, string $configuration): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Fixtures/config/Yaml/'));
        $loader->load($configuration.'.yml');
    }
}
