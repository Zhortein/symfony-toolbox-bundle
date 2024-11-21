<?php

namespace Zhortein\SymfonyToolboxBundle\DependencyInjection;

use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Zhortein\SymfonyToolboxBundle\Attribute\AsDatatable;
use Zhortein\SymfonyToolboxBundle\Attribute\AsHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Datatables\AbstractDatatable;
use Zhortein\SymfonyToolboxBundle\Doctrine\DBAL\Types\EnumActionType;
use Zhortein\SymfonyToolboxBundle\Service\AbstractHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\DatatableManager;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviderManager;
use Zhortein\SymfonyToolboxBundle\Service\StringTools;

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
        $attribute = $class->getAttributes(AsDatatable::class);
        if ($attribute) {
            $instance = $attribute[0]->newInstance();
            $serviceId = $class->getName();
            $sanitizedName = StringTools::sanitizeFileName($instance->name);
            $this->datatables[$sanitizedName] = new Reference($serviceId);

            return true;
        }

        return false;
    }

    private function registerDatatables(ContainerBuilder $container): void
    {
        if ($container->hasDefinition(DatatableManager::class)) {
            $container->getDefinition(DatatableManager::class)
                ->setArgument(0, $this->datatables);
        }
    }

    /**
     * Detect Holiday Providers via attribute #[AsHolidayProvider].
     *
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
