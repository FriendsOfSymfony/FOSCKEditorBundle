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
        if (\method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('fos_ck_editor');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('fos_ck_editor');
        }

        $rootNode
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
            ->arrayPrototype()
                ->normalizeKeys(false)
                ->useAttributeAsKey('name')
                ->variablePrototype()->end()
            ->end();
    }

    private function createPluginsNode(): ArrayNodeDefinition
    {
        return $this->createPrototypeNode('plugins')
            ->arrayPrototype()
                ->children()
                    ->scalarNode('path')->end()
                    ->scalarNode('filename')->end()
                ->end()
            ->end();
    }

    private function createStylesNode(): ArrayNodeDefinition
    {
        return $this->createPrototypeNode('styles')
            ->arrayPrototype()
                ->arrayPrototype()
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
            ->arrayPrototype()
                ->children()
                    ->scalarNode('imagesPath')->end()
                    ->arrayNode('templates')
                        ->arrayPrototype()
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
        $node = $this->createNode('filebrowsers')
            ->useAttributeAsKey('name')
            ->scalarPrototype()
            ->end();

        \assert($node instanceof ArrayNodeDefinition);

        return $node;
    }

    private function createToolbarsNode(): ArrayNodeDefinition
    {
        return $this->createNode('toolbars')
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('configs')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->variablePrototype()->end()
                    ->end()
                ->end()
                ->arrayNode('items')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->variablePrototype()->end()
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
        if (\method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder($name);
            $node = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $treeBuilder = new TreeBuilder();
            $node = $treeBuilder->root($name);
        }

        \assert($node instanceof ArrayNodeDefinition);

        return $node;
    }
}
