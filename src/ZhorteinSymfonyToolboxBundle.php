<?php

namespace Zhortein\SymfonyToolboxBundle;

use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\SymfonyToolboxCompilerPass;
use Zhortein\SymfonyToolboxBundle\DependencyInjection\ZhorteinSymfonyToolboxExtension;

class ZhorteinSymfonyToolboxBundle extends AbstractBundle
{
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (!$this->isAssetMapperAvailable($builder)) {
            return;
        }

        $builder->prependExtensionConfig('framework', [
            'asset_mapper' => [
                'paths' => [
                    __DIR__.'/../assets/dist' => '@zhortein/symfony-toolbox-bundle',
                ],
            ],
        ]);
    }

    private function isAssetMapperAvailable(ContainerBuilder $container): bool
    {
        if (!interface_exists(AssetMapperInterface::class)) {
            return false;
        }

        // check that FrameworkBundle 6.3 or higher is installed
        $bundlesMetadata = $container->getParameter('kernel.bundles_metadata');
        if (!isset($bundlesMetadata['FrameworkBundle'])) {
            return false;
        }

        return is_file($bundlesMetadata['FrameworkBundle']['path'].'/Resources/config/asset_mapper.php');
    }

    /**
     * @param array<int|string, mixed> $config
     *
     * @see https://symfony.com/doc/current/bundles/configuration.html#using-the-abstractbundle-class
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        try {
            $loader = new XmlFileLoader($builder, new FileLocator(__DIR__.'/../config'));
            $loader->load('services.xml');
        } catch (\Exception) {
        }
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new SymfonyToolboxCompilerPass());
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new ZhorteinSymfonyToolboxExtension();
        }

        return false !== $this->extension ? $this->extension : null;
    }
}
