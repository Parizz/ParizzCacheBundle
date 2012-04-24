Using ParizzCacheBundle
===================

* [Installation](#installation)
* [Cache drivers](#cache_drivers)

<a name="installation"></a>

## Installation

### Step 1) Get the bundle

First, grab ParizzCacheBundle. There are two different ways to do this:

#### Method a) Using the `deps` file

Add the following lines to your  `deps` file and then run `php bin/vendors install`:

```
[ParizzCacheBundle]
    git=git://github.com/Parizz/ParizzCacheBundle.git
    target=bundles/Parizz/CacheBundle
```

#### Method b) Using submodules

Run the following commands to bring in the needed libraries as submodules.

```bash
git submodule add git://github.com/Parizz/ParizzCacheBundle.git vendor/bundles/Parizz/CacheBundle
```

### Step 2) Register the namespaces

Add the following two namespace entries to the `registerNamespaces` call
in your autoloader:

``` php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'Parizz' => __DIR__.'/../vendor/bundles',
    // ...
));
```

### Step 3) Register the bundle

To start using the bundle, register it in your Kernel:

``` php
<?php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Parizz\CacheBundle\ParizzCacheBundle(),
    );
    // ...
)
```

<a name="cache_drivers"></a>

## Cache drivers

You can enable and use Doctrine cache drivers this way :

```yml
# app/config/config.yml
parizz_cache:
    drivers:
        memcache:
            type: memcache
            host: localhost
            port: 11211
        # A Filsesystem driver is also available
        # (for shared hosted who cannot enable APC, Memcache, etc...)
        file:
            type: filesystem
            path: /my/filesystem/cache/path
```

Then, just grab your cache service from the container :

```php
<?php
// Getting the Memcache driver
$cache = $container->get('parizz_cache.memcache_driver');

// Storing a value
$cache->save('key', 'value');

// Fetching a value
$value = $cache->fetch('key');
```