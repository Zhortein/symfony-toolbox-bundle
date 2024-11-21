<?php

namespace Zhortein\SymfonyToolboxBundle\DependencyInjection;

use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Zhortein\SymfonyToolboxBundle\Attribute\AsDatatable;
use Zhortein\SymfonyToolboxBundle\Attribute\AsHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;
use Zhortein\SymfonyToolboxBundle\Datatables\DatatableService;
use Zhortein\SymfonyToolboxBundle\Doctrine\DBAL\Types\EnumActionType;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviderManager;
use Zhortein\SymfonyToolboxBundle\Service\StringTools;

class SymfonyToolboxCompilerPass implements CompilerPassInterface
{
    /**
     * @var array<string, Reference>
     */
    private array $holidayProviders = [];

    public function process(ContainerBuilder $container): void
    {
        $haveHolidayProviders = false;
        foreach ($container->getDefinitions() as $definition) {
            $class = $definition->getClass();

            if (!$class || !class_exists($class)) {
                continue;
            }

            $reflClass = new \ReflectionClass($class);
            // Some detected features like Datatables excludes other Toolbox attributes so we optimize by bypassing other detections.
            if (!$this->detectDatatables($container, $definition, $reflClass)) {
                if ($this->detectHolidayProvider($reflClass)) {
                    $haveHolidayProviders = true;
                }
            }
        }

        if ($haveHolidayProviders) {
            $this->registerHolidayProviders($container);
        }
        $this->detectActionEnum($container);
        $this->registerDBALTypes($container);
    }

    /**
     * @param \ReflectionClass<object> $class
     */
    private function detectDatatables(ContainerBuilder $container, Definition $definition, \ReflectionClass $class): bool
    {
        $datatableServiceDefinition = $container->findDefinition(DatatableService::class);
        $attribute = $class->getAttributes(AsDatatable::class);
        if ($attribute) {
            $instance = $attribute[0]->newInstance();
            if (!$instance instanceof AbstractDatatable) {
                return false;
            }

            $datatableServiceDefinition->addMethodCall('registerDatatable', [
                $instance->name,
                [
                    'columns' => $instance->getColumns(),
                    'defaultPageSize' => $instance->defaultPageSize,
                    'defaultSort' => $instance->defaultSort,
                    'searchable' => $instance->searchable,
                    'options' => $instance->getOptions(),
                ],
                new Reference($class),
            ]);

            $sanitizedName = StringTools::sanitizeFileName($instance->name);
            $definition->addTag('zhortein.datatable', ['id' => $sanitizedName]);

            return true;
        }

        return false;
    }

    /**
     * @param \ReflectionClass<object> $class
     */
    private function detectHolidayProvider(\ReflectionClass $class): bool
    {
        $attribute = $class->getAttributes(AsHolidayProvider::class);
        if (!$attribute) {
            return false;
        }

        $instance = $attribute[0]->newInstance();
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
            $holidayProviderRefs = array_filter($this->holidayProviders, static function ($providerRef) use ($container) {
                return $container->has((string) $providerRef);
            });

            $container->getDefinition(HolidayProviderManager::class)
                ->setArgument(0, $holidayProviderRefs);
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
