<?php

namespace Parizz\CacheBundle\Cache;

use Doctrine\Common\Cache\ApcCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Cache\WinCacheCache;
use Doctrine\Common\Cache\XcacheCache;
use Doctrine\Common\Cache\ZendDataCache;

/**
 * The CacheFactory class.
 */
class CacheFactory
{
    private $storages;

    public function __construct($configs = array())
    {
        foreach ($configs as $name => $config) {
            $this->initStorage($name, $config);
        }
    }

    public function getStorage($name)
    {
        if (!isset($this->storages[$name])) {
            throw new \RuntimeException('There is no storage named "'.$name.'".');
        }

        return $this->storages[$name];
    }
    
    private function initStorage($name, array $config)
    {
        switch ($config['type'])
        {
            case 'filesystem':
                $cache = new FilesystemCache();
                $cache->setPath($config['path']);
                break;
            case 'apc':
                $cache = new ApcCache();
                break;
            case 'array':
                $cache = new ArrayCache();
                break;
            case 'memcache':
                $cache = new MemcacheCache();
                $memcache = new \Memcache;
                if (false === $memcache->connect($config['host'], $config['port'])) {
                    throw new \RuntimeException('Unable to connect to Memcache (host "'.$config['host'].'").');
                }
                $cache->setMemcache($memcache);
                break;
            case 'memcached':
                $cache = new MemcachedCache();
                $memcached = new \Memcached;
                $cache->setMemcached($memcached);
                break;
            case 'wincache':
                $cache = new WinCacheCache();
                break;
            case 'xcache':
                $cache = new XcacheCache();
                break;
            case 'zenddata':
                $cache = new ZendDataCache();
                break;
            default:
                throw new \InvalidArgumentException('The "'.$config['type'].'" storage type doesn\'t exists.');
        }

        $this->storages[$name] = $cache;
    }
}