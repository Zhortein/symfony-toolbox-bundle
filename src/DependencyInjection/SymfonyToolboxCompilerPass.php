<?php

namespace Zhortein\SymfonyToolboxBundle\DependencyInjection;

use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Zhortein\SymfonyToolboxBundle\Attribute\AsHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Doctrine\DBAL\Types\EnumActionType;
use Zhortein\SymfonyToolboxBundle\Service\AbstractHolidayProvider;
use Zhortein\SymfonyToolboxBundle\Service\HolidayProviderManager;

class SymfonyToolboxCompilerPass implements CompilerPassInterface
{
    /**
     * @var array<string, AbstractHolidayProvider>
     */
    private array $holidayProviders = [];

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $definition) {
            $class = $definition->getClass();

            if (!$class || !class_exists($class)) {
                continue;
            }

            $reflClass = new \ReflectionClass($class);
            $this->detectHolidayProvider($reflClass);
        }

        $this->registerHolidayProviders($container);
        $this->detectActionEnum($container);
        $this->registerDBALTypes($container);
    }

    /**
     * @param \ReflectionClass<object> $class
     */
    private function detectHolidayProvider(\ReflectionClass $class): void
    {
        $attribute = $class->getAttributes(AsHolidayProvider::class);

        if ($attribute) {
            $instance = $attribute[0]->newInstance();
            try {
                $holidayProviderInstance = $class->newInstance();
                if (!$holidayProviderInstance instanceof AbstractHolidayProvider) {
                    return;
                }
            } catch (\ReflectionException) {
                return;
            }
            foreach ($instance->countryCodes as $countryCode) {
                $this->holidayProviders[$countryCode] = $holidayProviderInstance;
            }
        }
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
