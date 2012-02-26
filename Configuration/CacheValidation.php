<?php

namespace Parizz\CacheBundle\Configuration;

/**
 * The CacheValidation class handles the @CacheValidation annotation parts.
 *
 * @Annotation
 */
class CacheValidation
{
    /**
     * @var \Parizz\CacheBundle\Validation\ValidationProviderInterface
     */
    private $provider;

    /**
     * @var string
     */
    private $eTag;

    /**
     * @var \DateTime
     */
    private $lastModified;

    /**
     * Constructor.
     *
     * @param array $values Attributes from the annotation
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $values['provider'] = $values['value'];
        }

        if (!isset($values['provider'])) {
            throw new \InvalidArgumentException('No "provider" given for CacheValidation annotation');
        }

        $provider = new $values['provider'];

        $this->provider = $provider;
    }

    /**
     * Gets the Validation headers provider.
     *
     * @return \Parizz\CacheBundle\Validation\ValidationProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Gets the eTag value.
     *
     * @return string
     */
    public function getETag()
    {
        return $this->eTag;
    }

    /**
     * Sets the eTag value.
     *
     * @param string $eTag
     */
    public function setETag($eTag)
    {
        $this->eTag = $eTag;
    }

    /**
     * Gets the lastModified value.
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Sets the lastModified value.
     *
     * @param \DateTime $lastModified
     */
    public function setLastModified(\DateTime $lastModified)
    {
        $this->lastModified = $lastModified;
    }
}