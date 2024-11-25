<?php

namespace Zhortein\SymfonyToolboxBundle\DependencyInjection;

use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\AssetMapper\ImportMap\ImportMapManager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Zhortein\SymfonyToolboxBundle\Service\Datatables\DatatableManager;

class ZhorteinSymfonyToolboxExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $actionEnums = is_array($config['action_enums']) ? $config['action_enums'] : [];

        $container->setParameter('zhortein_symfony_toolbox.action_enums', $actionEnums);

        $datatableConfig = $config['datatables'] ?? [];
        $container->setParameter('zhortein_symfony_toolbox.datatables', $datatableConfig);

        if ($container->hasDefinition(DatatableManager::class)) {
            $container->getDefinition(DatatableManager::class)
                ->setArgument(2, $config['datatables'])
            ;
        }

        $iconLibrary = $config['datatables']['icons']['library'] ?? Configuration::ICON_LIBRARY_FONTAWESOME;
        $container->registerForAutoconfiguration(ImportMapManager::class)
            ->addMethodCall('addImport', [$this->getImportForLibrary($iconLibrary)]);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->configureAssetMapper($container);
    }

    private function configureAssetMapper(ContainerBuilder $container): void
    {
        if (!$this->isAssetMapperAvailable($container)) {
            return;
        }

        $container->prependExtensionConfig('framework', [
            'asset_mapper' => [
                'paths' => [
                    __DIR__.'/../../assets/dist' => '@zhortein/symfony-toolbox-bundle',
                ],
            ],
        ]);
    }

    private function isAssetMapperAvailable(ContainerBuilder $container): bool
    {
        if (!interface_exists(AssetMapperInterface::class)) {
            return false;
        }

        $frameworkBundle = $container->getParameter('kernel.bundles_metadata')['FrameworkBundle'] ?? null;

        return $frameworkBundle && is_file($frameworkBundle['path'].'/Resources/config/asset_mapper.php');
    }

    /**
     * Retourne l'import à ajouter pour une bibliothèque donnée.
     */
    private function getImportForLibrary(string $iconLibrary): string
    {
        return match ($iconLibrary) {
            'fontawesome' => '@fortawesome/fontawesome-free',
            'bootstrap' => 'bootstrap-icons',
            default => '',
        };
    }
}
