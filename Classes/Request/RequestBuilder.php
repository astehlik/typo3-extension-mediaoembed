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
     * Builds a request using the given configuration and the
     * given provider data.
     *
     * @param Provider $provider
     * @return HttpRequest
     */
    public function buildNextRequest($provider): HttpRequest
    {
        $request = GeneralUtility::makeInstance(HttpRequest::class);
        $request->injectConfiguration($this->configuration);
        $request->setEndpoint($provider->getEndpoint());
        return $request;
    }
}
