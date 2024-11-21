<?php

namespace Zhortein\SymfonyToolboxBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Routing\Loader\YamlFileLoader;

class ZhorteinSymfonyToolboxExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.xml');

        $routingLoader = new YamlFileLoader(new FileLocator(__DIR__.'/../../config'));
        $routingLoader->load('routes.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $actionEnums = is_array($config['action_enums']) ? $config['action_enums'] : [];

        $container->setParameter('zhortein_symfony_toolbox.action_enums', $actionEnums);

        $datatableConfig = $config['datatables'] ?? [];
        $container->setParameter('zhortein_symfony_toolbox.datatables', $datatableConfig);
    }
}
