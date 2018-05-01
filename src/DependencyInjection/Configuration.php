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
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = $this->createTreeBuilder();
        $treeBuilder
            ->root('fos_ck_editor')
            ->children()
                ->booleanNode('enable')->end()
                ->booleanNode('async')->end()
                ->booleanNode('auto_inline')->end()
                ->booleanNode('inline')->end()
                ->booleanNode('autoload')->end()
                ->booleanNode('jquery')->end()
                ->booleanNode('require_js')->end()
                ->booleanNode('input_sync')->end()
                ->scalarNode('base_path')->end()
                ->scalarNode('js_path')->end()
                ->scalarNode('jquery_path')->end()
                ->scalarNode('default_config')->end()
                ->append($this->createConfigsNode())
                ->append($this->createPluginsNode())
                ->append($this->createStylesNode())
                ->append($this->createTemplatesNode())
                ->append($this->createFilebrowsersNode())
                ->append($this->createToolbarsNode())
            ->end();

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition
     */
    private function createConfigsNode()
    {
        return $this->createPrototypeNode('configs')
            ->prototype('array')
                ->normalizeKeys(false)
                ->useAttributeAsKey('name')
                ->prototype('variable')->end()
            ->end();
    }

    /**
     * @return ArrayNodeDefinition
     */
    private function createPluginsNode()
    {
        return $this->createPrototypeNode('plugins')
            ->prototype('array')
                ->children()
                    ->scalarNode('path')->end()
                    ->scalarNode('filename')->end()
                ->end()
            ->end();
    }

    /**
     * @return ArrayNodeDefinition
     */
    private function createStylesNode()
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

    /**
     * @return ArrayNodeDefinition
     */
    private function createTemplatesNode()
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

    /**
     * @return ArrayNodeDefinition
     */
    private function createFilebrowsersNode()
    {
        return $this->createNode('filebrowsers')
            ->useAttributeAsKey('name')
            ->prototype('scalar')
            ->end();
    }

    /**
     * @return ArrayNodeDefinition
     */
    private function createToolbarsNode()
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

    /**
     * @param string $name
     *
     * @return ArrayNodeDefinition
     */
    private function createPrototypeNode($name)
    {
        return $this->createNode($name)
            ->normalizeKeys(false)
            ->useAttributeAsKey('name');
    }

    /**
     * @param string $name
     *
     * @return ArrayNodeDefinition
     */
    private function createNode($name)
    {
        return $this->createTreeBuilder()->root($name);
    }

    /**
     * @return TreeBuilder
     */
    private function createTreeBuilder()
    {
        return new TreeBuilder();
    }
}
