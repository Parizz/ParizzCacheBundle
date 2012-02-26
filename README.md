Using ParizzCacheBundle
===================

* [Installation](#installation)
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

<a name="cache_validation-annotation"></a>

## Use the CacheValidation annotation

If you're using cache validation, your controllers look probably like this:


```php
<?php
// src/Acme/DemoBundle/Controller/DefaultController.php
namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * My main action.
     *
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $lastModified = $this->container
            ->get('my.service')
            ->get('foo.last_modified');

        $response = new Response;
        $response->setLastModified($lastModified);

        if ($response->isNotModified($request)) {
            return $response;
        }

        return $this->render('AcmeDemoBundle:Default:index.html.twig');
    }
}
```

With the CacheValidation annotation, a controller class would look like that:

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

This way you can keep tiny controllers and also use the same code for
different actions sharing the same cache validation strategy.

**Note** You only need to extend `ContainerAware` if you need the service
container to be available via `$this->container`. You can also implement
`ContainerAwareInterface` instead of extending this class.