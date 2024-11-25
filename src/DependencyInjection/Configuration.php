<?php

namespace Zhortein\SymfonyToolboxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\PaginatorFactory;

class Configuration implements ConfigurationInterface
{
    public const string DEFAULT_DATATABLE_PAGINATOR = PaginatorFactory::PAGINATOR_CUSTOM;
    public const int DEFAULT_DATATABLE_ITEMS_PER_PAGE = 10;
    public const string DATATABLE_CSS_MODE_BOOTSTRAP = 'bootstrap';
    public const string DATATABLE_CSS_MODE_TAILWIND = 'tailwind';
    public const string DATATABLE_CSS_MODE_CUSTOM = 'custom';
    public const string ICON_LIBRARY_FONTAWESOME = 'fontawesome';
    public const string ICON_LIBRARY_BOOTSTRAP = 'bootstrap';
    public const string ICON_LIBRARY_CUSTOM = 'custom';
    public const string DEFAULT_DATATABLE_CSS_MODE = self::DATATABLE_CSS_MODE_BOOTSTRAP;

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
                        ->arrayNode('icons')
                            ->children()
                                ->scalarNode('library')
                                    ->defaultValue(self::ICON_LIBRARY_FONTAWESOME)
                                    ->validate()
                                        ->ifNotInArray([self::ICON_LIBRARY_FONTAWESOME, self::ICON_LIBRARY_BOOTSTRAP, self::ICON_LIBRARY_CUSTOM])
                                        ->thenInvalid('Invalid Icons library %s')
                                    ->end()
                                ->end()
                                ->arrayNode('custom_libraries')
                                ->end()
                            ->end()
                        ->end()
                        ->scalarNode('css_mode')
                            ->defaultValue(self::DEFAULT_DATATABLE_CSS_MODE)
                            ->validate()
                                ->ifNotInArray([self::DATATABLE_CSS_MODE_BOOTSTRAP, self::DATATABLE_CSS_MODE_TAILWIND, self::DATATABLE_CSS_MODE_CUSTOM])
                                ->thenInvalid('Invalid CSS mode %s')
                            ->end()
                        ->end()
                        ->integerNode('items_per_page')->defaultValue(self::DEFAULT_DATATABLE_ITEMS_PER_PAGE)->end()
                        ->scalarNode('paginator')
                            ->defaultValue(self::DEFAULT_DATATABLE_PAGINATOR)
                            ->validate()
                                ->ifNotInArray([PaginatorFactory::PAGINATOR_CUSTOM, PaginatorFactory::PAGINATOR_KNP])
                                ->thenInvalid('Invalid pagination mode: %s. Use "knp", or "custom".')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }

    public static function isCssModeValid($cssMode): bool
    {
        return in_array(
            $cssMode,
            [self::DATATABLE_CSS_MODE_BOOTSTRAP, self::DATATABLE_CSS_MODE_TAILWIND, self::DATATABLE_CSS_MODE_CUSTOM, self::DEFAULT_DATATABLE_CSS_MODE],
            true
        );
    }
}
