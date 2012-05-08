ParizzCacheBundle
=================

This bundle allows you to configure cache services on top of Doctrine Common Cache :

## Using it

You can enable and use Doctrine cache drivers through your project configuration this way :

```yml
# app/config/config.yml
parizz_cache:
    drivers:
        my_memcache:
            type: memcache
            host: localhost
            port: 11211
        # A Filsesystem driver is also available
        # (for shared hosted who cannot enable APC, Memcache, etc...)
        foo:
            type: filesystem
            path: /my/filesystem/cache/path
```

Then, just grab your cache service from the container :

```php
<?php
// Getting the Memcache driver
$cache = $container->get('parizz_cache.my_memcache_driver');

// Storing a value
$cache->save('key', 'value');

// Fetching a value
$value = $cache->fetch('key');
```
