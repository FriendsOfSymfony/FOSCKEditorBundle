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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
                ->booleanNode('enable')->defaultTrue()->end()
                ->scalarNode('base_path')->defaultValue('bundles/ivoryckeditor/')->end()
                ->scalarNode('js_path')->defaultValue('bundles/ivoryckeditor/ckeditor.js')->end()
                ->scalarNode('default_config')->end()
                ->arrayNode('configs')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->useAttributeAsKey('name')
                        ->prototype('variable')->end()
                    ->end()
                ->end()
                ->arrayNode('plugins')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('path')->end()
                            ->scalarNode('filename')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('styles')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->prototype('array')
                            ->children()
                                ->scalarNode('name')->end()
                                ->scalarNode('element')->end()
                                ->arrayNode('styles')
                                    ->useAttributeAsKey('name')
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('attributes')
                                    ->useAttributeAsKey('name')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('templates')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('imagesPath')->end()
                            ->arrayNode('templates')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('title')->end()
                                        ->scalarNode('image')->end()
                                        ->scalarNode('description')->end()
                                        ->scalarNode('html')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
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
