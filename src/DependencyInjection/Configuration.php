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
            ->arrayNode('datatables')
                ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('css_mode')
                            ->defaultValue('bootstrap') // 'bootstrap', 'tailwind', 'custom'
                            ->validate()
                                ->ifNotInArray(['bootstrap', 'tailwind', 'custom'])
                                ->thenInvalid('Invalid CSS mode %s')
                            ->end()
                        ->end()
                        ->integerNode('items_per_page')->defaultValue(10)->end()
                        ->scalarNode('paginatior')
                            ->defaultValue('custom') // Default mode
                            ->validate()
                                ->ifNotInArray(['knp', 'custom'])
                                ->thenInvalid('Invalid pagination mode: %s. Use "knp", or "custom".')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
