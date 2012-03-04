Using ParizzCacheBundle
===================

* [Installation](#installation)
* [Cache drivers](#cache_drivers)
* [CacheValidation annotation](#cache_validation-annotation)

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
Then in your controller :

```php
<?php
// src/Acme/DemoBundle/Controller/DefaultController.php
namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * My main action.
     *
     * @param Request $request
     */
    public function indexAction()
    {
        // Getting the Memcache driver
        $cache = $this->container->get('parizz_cache.memcache_driver');
        
        // Storing a value
        $cache->save('key', 'value');
        
        // Fetching a value
        $value = $cache->fetch('key');

        //...
    }
}
```

<a name="cache_validation-annotation"></a>

## CacheValidation annotation

You can use the CacheValidation annotation to keep tiny controllers.

```php
<?php
// src/Acme/DemoBundle/Controller/DefaultController.php
namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Parizz\CacheBundle\Configuration\CacheValidation;

class DefaultController extends Controller
{
    /**
     * My main action.
     *
     * @CacheValidation("Acme\DemoBundle\CacheValidation\FooProvider")
     */
    public function indexAction()
    {
        return $this->render('AcmeDemoBundle:Default:index.html.twig');
    }
}
```

And the FooProvider class:

```php
<?php
// src/Acme/DemoBundle/CacheValidation/FooProvider.php
namespace Acme\DemoBundle\CacheValidation;

use Symfony\Component\DependencyInjection\ContainerAware;
use Parizz\CacheBundle\Validation\ValidationProviderInterface;

class FooProvider extends ContainerAware implements ValidationProviderInterface
{
    public function process()
    {
        $lastModified = $this->container
            ->get('my.service')
            ->get('foo.last_modified');

        return array('lastModified' => $lastModified);
    }
}

```

**Note** You only need to extend `ContainerAware` if you need the service
container to be available via `$this->container`. You can also implement
`ContainerAwareInterface` instead of extending this class.