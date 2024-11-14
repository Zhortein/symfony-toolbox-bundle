<?php

namespace Zhortein\SymfonyToolboxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('zhortein_symfony_toolbox');

        $treeBuilder->getRootNode()
            ->children()
            ->arrayNode('action_enums')
            ->scalarPrototype()->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
