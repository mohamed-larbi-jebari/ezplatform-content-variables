<?php

namespace ContextualCode\EzPlatformContentVariablesBundle\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;

class EzPlatformContentVariablesExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('rest_api.yaml');
        $loader->load('ezrichtext_settings.yaml');
        $loader->load('default_settings.yaml');


        $processor = new ConfigurationProcessor($container, $this->getAlias());
        $processor->mapConfigArray('supported_routes', $config, ContextualizerInterface::UNIQUE);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependExtension($container, 'ezrichtext');
        $this->prependExtension($container, 'ezpublish');
        $this->prependExtension($container, 'bazinga_js_translation');
    }

    protected function prependExtension(ContainerBuilder $container, string $extension): void
    {
        $configFile = __DIR__ . '/../Resources/config/' . $extension . '.yaml';
        $container->prependExtensionConfig($extension, Yaml::parseFile($configFile));
        $container->addResource(new FileResource($configFile));
    }
}
