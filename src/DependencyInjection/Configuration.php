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
                ->booleanNode('autoload')->defaultTrue()->end()
                ->booleanNode('poweredBy')->defaultTrue()->end()
                ->booleanNode('resize')->defaultTrue()->end()
                ->scalarNode('base_path')->defaultValue('bundles/fosckeditor/')->end()
                ->scalarNode('js_path')->defaultValue('bundles/fosckeditor/ckeditor.js')->end()
                ->scalarNode('default_config')->defaultValue(null)->end()
                ->append($this->createConfigsNode())
                ->append($this->createPluginsNode())
//                ->append($this->createTemplateNode())
                ->append($this->createStylesNode())
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

//    private function createTemplateNode(): ArrayNodeDefinition
//    {
//        return $this->createPrototypeNode('template')
//            ->arrayPrototype()
//                ->children()
//                    ->scalarNode('imagesPath')->end()
//                    ->arrayNode('templates')
//                        ->arrayPrototype()
//                            ->children()
//                                ->scalarNode('title')->end()
//                                ->scalarNode('image')->end()
//                                ->scalarNode('description')->end()
//                                ->scalarNode('html')->end()
//                                ->scalarNode('template')->end()
//                                ->append($this->createPrototypeNode('template_parameters')->prototype('scalar')->end())
//                            ->end()
//                        ->end()
//                    ->end()
//                ->end()
//            ->end();
//    }

    private function createStylesNode(): ArrayNodeDefinition
    {
        return $this->createPrototypeNode('styles')
            ->variablePrototype()->end();
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
