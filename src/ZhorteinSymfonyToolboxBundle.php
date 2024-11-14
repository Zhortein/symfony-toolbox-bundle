<?php

namespace Zhortein\SymfonyToolboxBundle;

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
