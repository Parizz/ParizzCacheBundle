<?php

namespace Parizz\CacheBundle\Tests\DependencyInjection;

use Parizz\CacheBundle\DependencyInjection\ParizzCacheExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ParizzCacheExtensiontest extends \PHPUnit_Framework_TestCase
{
    public function testDrivers()
    {
        $container = new ContainerBuilder();
        $loader = new ParizzCacheExtension();
        $loader->load(array(array(
        	'drivers' => array(
        	    'file' => 'filesystem'
        	)
        )), $container);
        $this->assertTrue($container->hasDefinition('parizz_cache.file_driver'), 'The file driver is loaded');
    }
}