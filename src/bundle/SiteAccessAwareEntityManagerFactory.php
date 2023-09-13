<?php

/**
 * @author    Novactive <novaseobundle@novactive.com>
 */

namespace ContextualCode\EzPlatformContentVariablesBundle;

use Doctrine\Bundle\DoctrineBundle\Mapping\ContainerEntityListenerResolver;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\MissingMappingDriverImplementation;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\Persistence\ManagerRegistry as Registry;
use Ibexa\Bundle\Core\ApiLoader\RepositoryConfigurationProvider;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class SiteAccessAwareEntityManagerFactory
{
    /**
     * @var RepositoryConfigurationProvider
     */
    private RepositoryConfigurationProvider $repositoryConfigurationProvider;

    public function __construct(
        private readonly Registry $registry,
        RepositoryConfigurationProvider $repositoryConfigurationProvider,
        private readonly ContainerEntityListenerResolver $resolver,
        private readonly array $settings
    ) {
        $this->repositoryConfigurationProvider = $repositoryConfigurationProvider;
    }

    private function getConnectionName(): string
    {
        $config = $this->repositoryConfigurationProvider->getRepositoryConfig();

        return $config['storage']['connection'] ?? 'default';
    }

    /**
     * @throws MissingMappingDriverImplementation
     */
    public function get(): EntityManagerInterface
    {
        $connectionName = $this->getConnectionName();
        // If it is the default connection then we don't bother we can directly use the default entity Manager
        if ('default' === $connectionName) {
            return $this->registry->getManager();
        }

        $connection = $this->registry->getConnection($connectionName);

        /** @var Connection $connection */
        $cache = new ArrayAdapter();
        $config = new Configuration();
        $config->setMetadataCache($cache);
        $driverImpl = $config->newDefaultAnnotationDriver(__DIR__.'/../Entity', false);
        $config->setMetadataDriverImpl($driverImpl);
        $config-> setQueryCache($cache);

        $config->setProxyDir($this->settings['cache_dir'].'/eZContentVariablesBundle/');
        $config->setProxyNamespace('eZContentVariablesBundle\Proxies');
        $config->setAutoGenerateProxyClasses($this->settings['debug']);
        $config->setEntityListenerResolver($this->resolver);
        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        return new EntityManager($connection, $config);
    }
}
