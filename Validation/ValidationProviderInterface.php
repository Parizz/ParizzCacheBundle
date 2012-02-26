<?php

namespace Parizz\CacheBundle\Validation;

/**
 * The CacheValidationProviderInterface interface.
 */
interface ValidationProviderInterface
{
    /**
     * The process method is used to generate the eTag and/or lastModified values.
     *
     * @return array ex: array("eTag" => "azerty", "lastModified" => new \DateTime)
     */
    function process();
}
