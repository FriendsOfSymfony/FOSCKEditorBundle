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

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Ivory CKEditor configuration.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('ivory_ck_editor');

        $node
            ->children()
                ->arrayNode('configs')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->useAttributeAsKey('name')
                        ->prototype('variable')->end()
                    ->end()
                ->end()
                ->arrayNode('toolbars')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('configs')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->prototype('variable')->end()
                            ->end()
                        ->end()
                        ->arrayNode('items')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->prototype('variable')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
