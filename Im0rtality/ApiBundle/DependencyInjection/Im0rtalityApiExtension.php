<?php

namespace Im0rtality\ApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class Im0rtalityApiExtension extends Extension
{
    public function getAlias()
    {
        return 'im0rtality_api';
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('listeners.yml');
        $loader->load('actions.yml');

        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $configs);

        $container->setParameter('api_bundle_api.config.mapping', $config['mapping']);
        $container->setParameter('api_bundle_api.config.ownership', $config['ownership']);
        $container->setParameter('api_bundle_api.config.default_index_size', $config['index_size']);

        /* ACL */
        $acl = Yaml::parse(
            file_get_contents($container->getParameter('kernel.root_dir') . '/config/' . $config['acl'])
        );
        $container->setParameter('api_bundle_api.config.security.acl', $acl);

        $factory = $container->getDefinition('im0rtality_api.data_source.factory');

        switch ($config['data']['type']) {
            case 'orm':
                $dataSource = $container->getDefinition('im0rtality_api.data_source.orm');
                $dataSource->addMethodCall('setManagerName', [$config['data']['source']]);
                break;
            default:
                throw new InvalidConfigurationException("Unsupported type %s in rest_api.data.type");
        }

        $factory->addMethodCall('setDataSource', [$dataSource]);

        $container->setDefinition('im0rtality_api.data_source.factory', $factory);
    }
}
