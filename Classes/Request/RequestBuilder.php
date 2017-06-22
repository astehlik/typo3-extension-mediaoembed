<?php

namespace Sto\Mediaoembed\Request;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Exception\NoProviderEndpointException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Builds a request object based on the (TypoScript) configuration
 */
class RequestBuilder
{
    /**
     * TypoScript / Flexform configuration
     *
     * @var \Sto\Mediaoembed\Content\Configuration
     */
    protected $configuration;

    /**
     * Array of possible endpoints for the current provider.
     *
     * @var array
     */
    protected $endpoints;

    /**
     * The provider for which the request will be created
     *
     * @var Provider
     */
    protected $provider;

    /**
     * Request object that is build by this request builder
     *
     * @var HttpRequest
     */
    protected $request;

    /**
     * Builds a request using the given configuration and the
     * given provider data.
     *
     * @param Provider $provider
     * @return HttpRequest or FALSE if no further requests are available
     */
    public function buildNextRequest($provider)
    {
        $providerChanged = $this->initializeProvider($provider);

        // If provider has no further endpoints we return FALSE
        if (!$this->initializeEndpoints($providerChanged)) {
            return false;
        }

        $this->initializeNewRequest();
        return $this->request;
    }

    /**
     * Injector for the TypoScript / Flexform configuration
     *
     * @param \Sto\Mediaoembed\Content\Configuration $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Initializes the endpoints for the current provider.
     *
     * @param boolean $providerChanged if TRUE the endpoints array will be initialized with new endpoints from the
     *     current provider, otherwise the array pointer of the endpoints array will be moved forward.
     * @return boolean TRUE if endpoints are available, otherwise FALSE
     * @throws NoProviderEndpointException
     */
    protected function initializeEndpoints($providerChanged)
    {
        if ($providerChanged) {
            $this->endpoints = $this->provider->getAllEndpoints();

            if (!count($this->endpoints)) {
                throw new NoProviderEndpointException($this->provider);
            }

            reset($this->endpoints);
        } else {
            if (!next($this->endpoints)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Build a new request in the request property
     *
     * @return void
     */
    protected function initializeNewRequest()
    {
        /**
         * @var HttpRequest $request
         */
        $request = GeneralUtility::makeInstance(HttpRequest::class);
        $this->request = $request;
        $this->request->injectConfiguration($this->configuration);
        $this->request->setEndpoint(current($this->endpoints));
    }

    /**
     * Initializes the provider for which the request will be build
     *
     * @param Provider $provider
     * @return boolean TRUE if provider changes, otherwilse FALSE
     */
    protected function initializeProvider($provider)
    {
        if ($provider->equals($this->provider)) {
            return false;
        }

        $this->provider = $provider;
        return true;
    }
}
