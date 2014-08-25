<?php

namespace Tabernicola\JukeCloudBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('tabernicola_juke_cloud');

        $rootNode
            ->children()
                ->arrayNode('plugins')
                    ->children()
                        ->arrayNode('info')->prototype('scalar')->end()->end()
                        ->arrayNode('playlist')->prototype('scalar')->end()->end()
                        ->arrayNode('library')->prototype('scalar')->end()->end()
                    ->end()
                ->end() // twitter
            ->end()
        ;

        return $treeBuilder;
    }
}
