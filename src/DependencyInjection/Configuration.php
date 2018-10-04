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

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = $this->createTreeBuilder();
        $treeBuilder
            ->root('fos_ck_editor')
            ->children()
                ->booleanNode('enable')->defaultTrue()->end()
                ->booleanNode('async')->defaultFalse()->end()
                ->booleanNode('auto_inline')->defaultTrue()->end()
                ->booleanNode('inline')->defaultFalse()->end()
                ->booleanNode('autoload')->defaultTrue()->end()
                ->booleanNode('jquery')->defaultFalse()->end()
                ->booleanNode('require_js')->defaultFalse()->end()
                ->booleanNode('input_sync')->defaultFalse()->end()
                ->scalarNode('base_path')->defaultValue('bundles/fosckeditor/')->end()
                ->scalarNode('js_path')->defaultValue('bundles/fosckeditor/ckeditor.js')->end()
                ->scalarNode('jquery_path')->defaultValue('bundles/fosckeditor/adapters/jquery.js')->end()
                ->scalarNode('default_config')->defaultValue(null)->end()
                ->append($this->createConfigsNode())
                ->append($this->createPluginsNode())
                ->append($this->createStylesNode())
                ->append($this->createTemplatesNode())
                ->append($this->createFilebrowsersNode())
                ->append($this->createToolbarsNode())
            ->end();

        return $treeBuilder;
    }

    private function createConfigsNode(): ArrayNodeDefinition
    {
        return $this->createPrototypeNode('configs')
            ->prototype('array')
                ->normalizeKeys(false)
                ->useAttributeAsKey('name')
                ->prototype('variable')->end()
            ->end();
    }

    private function createPluginsNode(): ArrayNodeDefinition
    {
        return $this->createPrototypeNode('plugins')
            ->prototype('array')
                ->children()
                    ->scalarNode('path')->end()
                    ->scalarNode('filename')->end()
                ->end()
            ->end();
    }

    private function createStylesNode(): ArrayNodeDefinition
    {
        return $this->createPrototypeNode('styles')
            ->prototype('array')
                ->prototype('array')
                    ->children()
                        ->scalarNode('name')->end()
                        ->scalarNode('type')->end()
                        ->scalarNode('widget')->end()
                        ->variableNode('element')->end()
                        ->append($this->createPrototypeNode('styles')->prototype('scalar')->end())
                        ->append($this->createPrototypeNode('attributes')->prototype('scalar')->end())
                    ->end()
                ->end()
            ->end();
    }

    private function createTemplatesNode(): ArrayNodeDefinition
    {
        return $this->createPrototypeNode('templates')
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
                                ->scalarNode('template')->end()
                                ->append($this->createPrototypeNode('template_parameters')->prototype('scalar')->end())
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    private function createFilebrowsersNode(): ArrayNodeDefinition
    {
        return $this->createNode('filebrowsers')
            ->useAttributeAsKey('name')
            ->prototype('scalar')
            ->end();
    }

    private function createToolbarsNode(): ArrayNodeDefinition
    {
        return $this->createNode('toolbars')
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
            ->end();
    }

    private function createPrototypeNode(string $name): ArrayNodeDefinition
    {
        return $this->createNode($name)
            ->normalizeKeys(false)
            ->useAttributeAsKey('name');
    }

    private function createNode(string $name): ArrayNodeDefinition
    {
        return $this->createTreeBuilder()->root($name);
    }

    private function createTreeBuilder(): TreeBuilder
    {
        return new TreeBuilder();
    }
}
