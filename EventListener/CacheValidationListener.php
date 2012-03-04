<?php

namespace Parizz\CacheBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\Response;
use Parizz\CacheBundle\Configuration\CacheValidation;
use Parizz\CacheBundle\Validation\ValidationProviderInterface;

class CacheValidationListener
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor.
     *
     * @param Reader $reader An Reader instance
     * @param ContainerInterFace $container The container
     */
    public function __construct(Reader $reader, ContainerInterFace $container)
    {
        $this->reader    = $reader;
        $this->container = $container;
    }

    /**
     * Modifies the Request object to apply cache validation configuration provider
     *
     * @param FilterControllerEvent $event A FilterControllerEvent instance
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!is_array($controller = $event->getController())) {
            return;
        }

        $object = new \ReflectionObject($controller[0]);
        $method = $object->getMethod($controller[1]);

        foreach ($this->reader->getMethodAnnotations($method) as $configuration) {
            if ($configuration instanceof CacheValidation) {
                $event->getRequest()->attributes->set('_cache_validation', $configuration);

                $response = $this->populateResponse($configuration);

                // if the response is valid, we return it
                if ($response->isNotModified($event->getRequest())) {
                    $returnNotModifiedResponse = function() use ($response) {
                        return $response;
                    };

                    $event->setController($returnNotModifiedResponse);
                }
            }
        }
    }

    /**
     * Modifies the response to apply HTTP expiration/validation header fields.
     *
     * @param FilterResponseEvent $event The notified event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$configuration = $event->getRequest()->attributes->get('_cache_validation')) {
            return;
        }

        $response = $event->getResponse();

        if (!$response->isSuccessful()) {
            return;
        }

        $this->populateResponse($configuration, $response);

        $event->setResponse($response);
    }

    /**
     * Modifies or create a response and apply HTTP validation header fields
     *
     * @param CacheValidation $configuration The annotation configuration
     * @param Response $response The response to populate
     */
    private function populateResponse($configuration, $response = null)
    {
        if (!$response) {
            $response = new Response;
            $provider = $configuration->getProvider();

            if (!$provider instanceof ValidationProviderInterface) {
                throw new \RuntimeException('A validation provider has to implement the ValidationProviderInterface.');
            }

            if ($provider instanceof ContainerAwareInterface) {
                $provider->setContainer($this->container);
            }

            $validationHeaders = $provider->process();

            if (isset($validationHeaders['eTag'])) {
                $configuration->setETag($validationHeaders['eTag']);
            }

            if (isset($validationHeaders['lastModified'])) {
                if (!$validationHeaders['lastModified'] instanceof \DateTime) {
                    throw new \RuntimeException('A Last-Modified header has to be an instance of a \Datetime object, ' . gettype($validationHeaders['lastModified']) . ' given.');
                }
                $configuration->setLastModified($validationHeaders['lastModified']);
            }
        }

        if ($configuration->getETag()) {
            $response->setETag($configuration->getETag());
        }

        if ($configuration->getLastModified()) {
            $response->setLastModified($configuration->getLastModified());
        }

        return $response;
    }
}