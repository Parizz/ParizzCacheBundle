<?php

namespace Parizz\CacheBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ParizzCacheExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('cache.xml');

        foreach ($config['drivers'] as $name => $options) {
            $cacheDef = $this->getCacheDriverDefinition($options);
            $container->setDefinition(sprintf('parizz_cache.%s_driver', $name), $cacheDef);
        }
    }

    protected function getCacheDriverDefinition(array $config)
    {
        switch ($config['type']) {
            case 'memcache':
                $memcacheInstance = new Definition('Memcache');
                $memcacheInstance->addMethodCall('connect', array(
                    $config['host'], $config['port']
                ));
                $cacheDef = new Definition('%parizz_cache.memcache_driver.class%');
                $cacheDef->addMethodCall('setMemcache', array($memcacheInstance));
                break;
            case 'memcached':
                $cacheDef = new Definition('%parizz_cache.memcached_driver.class%');
                $cacheDef->addMethodCall('setMemcached', array(
                    new Definition('Memcached')
                ));
                break;
            case 'filesystem':
                $cacheDef = new Definition('%parizz_cache.filesystem_driver.class%');
                $cacheDef->addMethodCall('setPath', array(
                    $config['path']
                ));
                break;
            case 'apc':
            case 'array':
            case 'win_cache':
            case 'xcache':
            case 'zend_data':
                $cacheDef = new Definition('%parizz_cache.'.$config['type'].'_driver.class%');
                break;
            default:
                throw new \InvalidArgumentException(sprintf('"%s" is an unrecognized cache driver.', $config['type']));
        }

        $cacheDef->addMethodCall('setNamespace', array($config['namespace']));

        return $cacheDef;
    }
}
