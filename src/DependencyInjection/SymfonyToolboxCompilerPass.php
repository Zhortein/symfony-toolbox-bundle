<?php

namespace Zhortein\SymfonyToolboxBundle\DependencyInjection;

use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Zhortein\SymfonyToolboxBundle\Attribute\AsDatatable;
use Zhortein\SymfonyToolboxBundle\Attribute\AsHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Doctrine\DBAL\Types\EnumActionType;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\ColumnDTO;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\DatatableOptionsDTO;
use Zhortein\SymfonyToolboxBundle\DTO\Datatables\GlobalOptionsDTO;
use Zhortein\SymfonyToolboxBundle\Service\Cache\CacheManager;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\DatatableManager;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviderManager;

class SymfonyToolboxCompilerPass implements CompilerPassInterface
{
    /**
     * @var array<string, Reference>
     */
    private array $holidayProviders = [];

    /**
     * @var array<string, Reference>
     */
    private array $datatables = [];

    /**
     * @var array<string, ColumnDTO[]>
     */
    private array $datatablesColumns = [];

    /**
     * @var DatatableOptionsDTO[]
     */
    private array $datatablesOptions = [];

    private GlobalOptionsDTO $datatableGlobalOptions;

    public function process(ContainerBuilder $container): void
    {
        $haveHolidayProviders = false;

        /**
         * @var array{
         *        css_mode: string,
         *        items_per_page: int,
         *        paginator: string,
         *        export: array{
         *             enabled_by_default: bool,
         *             export_csv: bool,
         *             export_pdf: bool,
         *             export_excel: bool,
         *        },
         *        ux_icons: bool,
         *        ux_icons_options: array{
         *             icon_first: string,
         *             icon_previous: string,
         *             icon_next: string,
         *             icon_last: string,
         *             icon_search: string,
         *             icon_true: string,
         *             icon_false: string,
         *             icon_sort_neutral: string,
         *             icon_sort_asc: string,
         *             icon_sort_desc: string,
         *             icon_filter: string,
         *             icon_export_csv: string,
         *             icon_export_pdf: string,
         *             icon_export_excel: string,
         *        }
         *    } $globalOptions
         */
        $globalOptions = $container->getParameter('zhortein_symfony_toolbox.datatables');
        $this->datatableGlobalOptions = GlobalOptionsDTO::fromArray($globalOptions);

        foreach ($container->getDefinitions() as $definition) {
            $class = $definition->getClass();

            if (!$class || !class_exists($class) || str_contains($class, 'class@anonymous')) {
                continue;
            }

            $reflClass = new \ReflectionClass($class);
            // Some detected features like Datatables excludes other Toolbox attributes so we optimize by bypassing other detections.
            if (!$this->detectDatatables($reflClass)) {
                if ($this->detectHolidayProvider($reflClass)) {
                    $haveHolidayProviders = true;
                }
            }
        }

        if ($haveHolidayProviders) {
            $this->registerHolidayProviders($container);
        }
        $this->registerDatatables($container);
        $this->detectActionEnum($container);
        $this->registerDBALTypes($container);
    }

    /**
     * Detect Datatables via attribute #[AsDatatable].
     *
     * @param \ReflectionClass<object> $class
     */
    private function detectDatatables(\ReflectionClass $class): bool
    {
        /** @var AsDatatable|null $instance */
        $instance = $class->getAttributes(AsDatatable::class)[0] ?? null;
        if ($instance) {
            $serviceId = $class->getName();

            /**
             * @var array{
             *      columns: array<int, array{
             *          name: string,
             *          label: string,
             *          searchable?: bool,
             *          sortable?: bool,
             *          nameAs?: string,
             *          alias?: string,
             *          sqlAlias?: string,
             *          datatype?: string,
             *          template?: string,
             *          header?: array{
             *              translate?: bool,
             *              keep_default_classes?: bool,
             *              class?: string,
             *              data?: array<string, string|int|float|bool|null>
             *          },
             *          dataset?: array{
             *              translate?: bool,
             *              keep_default_classes?: bool,
             *              class?: string,
             *              data?: array<string, string|int|float|bool|null>
             *          },
             *          footer?: array{
             *              translate?: bool,
             *              keep_default_classes?: bool,
             *              class?: string,
             *              data?: array<string, string|int|float|bool|null>
             *          },
             *          autoColumns?: bool,
             *          isEnum?: bool,
             *          isTranslatableEnum?: bool
             *      }>,
             *      name: string,
             *      defaultPageSize?: int,
             *      defaultSort?: array<int, array{
             *           field: string,
             *           order: string
             *      }>,
             *      searchable?: bool,
             *      sortable?: bool,
             *      exportable?: bool,
             *      exportCsv?: bool,
             *      exportPdf?: bool,
             *      exportExcel?: bool,
             *      autoColumns?: bool,
             *      translationDomain?: string,
             *      actionColumn?: array{
             *          label?: string,
             *          template?: string
             *      },
             *      selectorColumn?: array{
             *          label?: string,
             *          template?: string
             *      },
             *      options?: array{
             *       thead?: array{
             *         translate?: bool,
             *         keep_default_classes?: bool,
             *         class?: string,
             *         data?: array<string, string|int|float|bool|null>,
             *     },
             *       tbody?: array{
             *         translate?: bool,
             *         keep_default_classes?: bool,
             *         class?: string,
             *         data?: array<string, string|int|float|bool|null>,
             *     },
             *       tfoot?: array{
             *         translate?: bool,
             *         keep_default_classes?: bool,
             *         class?: string,
             *         data?: array<string, string|int|float|bool|null>,
             *     },
             *   }
             *  } $attrOptions
             */
            $attrOptions = $instance->toArray();
            if (!array_key_exists('name', $attrOptions) || !array_key_exists('columns', $attrOptions) || !is_array($attrOptions['columns'])) {
                throw new \InvalidArgumentException('Datatable name and columns must be defined for '.$instance->name);
            }
            $this->datatables[$instance->name] = new Reference($serviceId);
            $this->datatablesColumns[$instance->name] = [];

            foreach ($attrOptions['columns'] as $column) {
                $this->datatablesColumns[$instance->name][] = ColumnDTO::fromArray($column);
            }
            $this->datatablesOptions[$instance->name] = DatatableOptionsDTO::fromArray($attrOptions, $this->datatableGlobalOptions);

            return true;
        }

        return false;
    }

    private function registerDatatables(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(DatatableManager::class)) {
            $container->getDefinition(DatatableManager::class)
                ->setArgument(0, $this->datatables)
                ->setArgument(1, $this->datatablesColumns)
                ->setArgument(2, $this->datatablesOptions)
                ->setArgument(3, $this->datatableGlobalOptions)
                ->setArgument(4, new Reference(CacheManager::class))
            ;
        }
    }

    /**
     * Detect Holiday Providers via attribute #[AsHolidayProvider].
     *
     * @param \ReflectionClass<object> $class
     */
    private function detectHolidayProvider(\ReflectionClass $class): bool
    {
        /** @var AsHolidayProvider|null $instance */
        $instance = $class->getAttributes(AsHolidayProvider::class)[0] ?? null;
        if (!$instance) {
            return false;
        }

        $countryCodes = $instance->countryCodes;
        $serviceId = $class->getName();
        foreach ($countryCodes as $countryCode) {
            $this->holidayProviders[strtoupper($countryCode)] = new Reference($serviceId);
        }

        return !empty($countryCodes);
    }

    private function registerHolidayProviders(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(HolidayProviderManager::class)) {
            $container->getDefinition(HolidayProviderManager::class)
                ->setArgument(0, $this->holidayProviders);
        }
    }

    private function detectActionEnum(ContainerBuilder $container): void
    {
        // Récupérer les enums configurées et les ajouter au type DBAL
        /** @var string[] $actionEnums */
        $actionEnums = $container->getParameter('zhortein_symfony_toolbox.action_enums');

        foreach ($actionEnums as $enumClass) {
            if (enum_exists($enumClass)) {
                EnumActionType::addEnumClass($enumClass);
            }
        }
    }

    private function registerDBALTypes(ContainerBuilder $container): void
    {
        /** @var Type[] $typeDefinitions */
        $typeDefinitions = $container->getParameter('doctrine.dbal.connection_factory.types');
        $typeDefinitions[EnumActionType::NAME] = ['class' => EnumActionType::class];
        $container->setParameter('doctrine.dbal.connection_factory.types', $typeDefinitions);
    }
}
