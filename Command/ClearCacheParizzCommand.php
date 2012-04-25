<?php

namespace Parizz\CacheBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheParizzCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('parizz:cache:clear')
            ->setDescription('Clears the cache for a given doctrine cache driver')
            ->addArgument('name', InputArgument::REQUIRED, 'The cache driver name')
            ->addArgument('key', InputArgument::OPTIONAL, 'The cache key to delete')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $cacheServiceName = sprintf('parizz_cache.%s_driver', $input->getArgument('name'));

        if (!$container->has($cacheServiceName)) {
            throw new \Exception(sprintf('"%s" isn\'t a recognized doctrine cache driver name.', $driverName));
        }

        if ($cacheKey = $input->getArgument('key')) {
            $container->get($cacheServiceName)->delete($cacheKey);

            return;
        }

        $container->get($cacheServiceName)->flushAll();
    }
}