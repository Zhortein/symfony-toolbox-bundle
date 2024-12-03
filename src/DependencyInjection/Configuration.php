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
    public const string DEFAULT_DATATABLE_CSS_MODE = self::DATATABLE_CSS_MODE_BOOTSTRAP;

    /**
     * @var array{
     *      css_mode: string,
     *      items_per_page: int,
     *      paginator: string,
     *      ux_icons: bool,
     *      ux_icons_options: array{
     *           icon_first: string,
     *           icon_previous: string,
     *           icon_next: string,
     *           icon_last: string,
     *           icon_search: string,
     *           icon_true: string,
     *           icon_false: string,
     *           icon_sort_neutral: string,
     *           icon_sort_asc: string,
     *           icon_sort_desc: string,
     *           icon_filter: string,
     *      }
     *  }
     */
    public const array DEFAULT_CONFIGURATION = [
        'css_mode' => self::DEFAULT_DATATABLE_CSS_MODE,
        'items_per_page' => self::DEFAULT_DATATABLE_ITEMS_PER_PAGE,
        'paginator' => self::DEFAULT_DATATABLE_PAGINATOR,
        'ux_icons' => true,
        'ux_icons_options' => [
            'icon_first' => 'bi:chevron-double-left',
            'icon_previous' => 'bi:chevron-left',
            'icon_next' => 'bi:chevron-right',
            'icon_last' => 'bi:chevron-double-right',
            'icon_search' => 'bi:search',
            'icon_true' => 'bi:check',
            'icon_false' => 'bi:x',
            'icon_sort_neutral' => 'mdi:sort',
            'icon_sort_asc' => 'bi:sort-alpha-down',
            'icon_sort_desc' => 'bi:sort-alpha-up',
            'icon_filter' => 'mi:filter',
        ],
    ];

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('zhortein_symfony_toolbox');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('action_enums')
                    ->scalarPrototype()
                ->end()
            ->end()
            ->arrayNode('datatables')
                ->addDefaultsIfNotSet()
                    ->children()
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
                        ->booleanNode('ux_icons')->defaultValue(true)->end()
                        ->arrayNode('ux_icons_options')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('icon_first')->defaultValue('bi:chevron-double-left')->end()
                                ->scalarNode('icon_previous')->defaultValue('bi:chevron-left')->end()
                                ->scalarNode('icon_next')->defaultValue('bi:chevron-right')->end()
                                ->scalarNode('icon_last')->defaultValue('bi:chevron-double-right')->end()
                                ->scalarNode('icon_search')->defaultValue('bi:search')->end()
                                ->scalarNode('icon_true')->defaultValue('bi:check')->end()
                                ->scalarNode('icon_false')->defaultValue('bi:x')->end()
                                ->scalarNode('icon_sort_neutral')->defaultValue('mdi:sort')->end()
                                ->scalarNode('icon_sort_asc')->defaultValue('bi:sort-alpha-down')->end()
                                ->scalarNode('icon_sort_desc')->defaultValue('bi:sort-alpha-up')->end()
                                ->scalarNode('icon_filter')->defaultValue('mi:filter')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }

    /**
     * Validates if the provided CSS mode is valid.
     *
     * @param mixed $cssMode the CSS mode to validate
     *
     * @return bool returns true if the CSS mode is valid, otherwise false
     */
    public static function isCssModeValid(mixed $cssMode): bool
    {
        if (!is_string($cssMode)) {
            return false;
        }

        return in_array(
            $cssMode,
            [self::DATATABLE_CSS_MODE_BOOTSTRAP, self::DATATABLE_CSS_MODE_TAILWIND, self::DATATABLE_CSS_MODE_CUSTOM, self::DEFAULT_DATATABLE_CSS_MODE],
            true
        );
    }
}
