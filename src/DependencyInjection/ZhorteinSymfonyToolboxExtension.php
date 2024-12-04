<?php

namespace Zhortein\SymfonyToolboxBundle\DependencyInjection;

use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Filesystem\Filesystem;
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
         *  } $config
         */
        $datatableConfig = $config['datatables'] ?? [];
        $container->setParameter('zhortein_symfony_toolbox.datatables', $datatableConfig);

        if ($container->hasDefinition(DatatableManager::class)) {
            $container->getDefinition(DatatableManager::class)
                ->setArgument(2, $config['datatables'])
            ;
        }

        $this->handleBundleRoutes($container);
    }

    protected function handleBundleRoutes(ContainerBuilder $container): void
    {
        $filesystem = new Filesystem();
        $filePath = $container->getParameter('kernel.project_dir') . '/config/routes/zhortein_symfony_toolbox.yaml';

        if (!$filesystem->exists($filePath)) {
            $filesystem->dumpFile($filePath, <<<YAML
zhortein_symfony_toolbox:
    resource: '@ZhorteinSymfonyToolboxBundle/config/routes.yaml'
YAML);
        }
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

        /** @var array<string, string|int|bool|float|null> $frameworkBundle */
        $frameworkBundle = $container->getParameter('kernel.bundles_metadata')['FrameworkBundle'] ?? null;

        return $frameworkBundle && is_file($frameworkBundle['path'].'/Resources/config/asset_mapper.php');
    }
}
