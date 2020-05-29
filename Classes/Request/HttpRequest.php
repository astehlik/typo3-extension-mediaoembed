<?php
declare(strict_types=1);

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

use RuntimeException;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Exception\HttpClientRequestException;
use Sto\Mediaoembed\Exception\HttpNotFoundException;
use Sto\Mediaoembed\Exception\HttpNotImplementedException;
use Sto\Mediaoembed\Exception\HttpUnauthorizedException;
use Sto\Mediaoembed\Request\HttpClient\HttpClientFactory;
use Sto\Mediaoembed\Request\HttpClient\HttpClientInterface;
use TYPO3\CMS\Core\Service\MarkerBasedTemplateService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Represents a HTTP request
 */
class HttpRequest
{
    /**
     * The configuration
     *
     * @var Configuration
     */
    private $configuration;

    /**
     * The endpoint URL that should be contacted to get the embed
     * information.
     *
     * @var string
     */
    private $endpoint;

    /**
     * The required response format. When not specified, the provider can return
     * any valid response format.
     * When specified, the provider must return data in the request format,
     * else return an error (see below for error codes).
     * This value is optional.
     *
     * Important! At the moment, we only handle JSON formatted Responses.
     *
     * @var string
     */
    private $format = 'json';

    private $httpClientFactory;

    public function __construct(Configuration $configuration, string $endpoint)
    {
        $this->configuration = $configuration;
        $this->endpoint = $endpoint;
    }

    public function injectHttpClientFactory(HttpClientFactory $httpClientFactory)
    {
        $this->httpClientFactory = $httpClientFactory;
    }

    /**
     * Builds a request url and tries to read the embed information
     * from the server. Result should be in json format.
     *
     * @return string json formatted result from server
     */
    public function sendAndGetResponseData()
    {
        $parameters = $this->buildRequestParameterArray();
        $requestUrl = $this->buildRequestUrl($parameters);
        return $this->sendRequest($requestUrl);
    }

    /**
     * Builds an array of parameters that should be attached to the
     * endpoint url.
     *
     * @return array
     */
    protected function buildRequestParameterArray(): array
    {
        $parameters = [];

        $maxwidth = $this->configuration->getMaxwidth();
        if ($maxwidth > 0) {
            $parameters['maxwidth'] = $maxwidth;
        }

        $maxheight = $this->configuration->getMaxheight();
        if ($maxheight > 0) {
            $parameters['maxheight'] = $maxheight;
        }

        if (isset($this->format)) {
            $parameters['format'] = $this->format;
        }
        // Needs to be last parameter
        $parameters['url'] = $this->configuration->getMediaUrl();

        return $parameters;
    }

    /**
     * Builds a request url for the current endpoint based on the
     * given parameter array.
     *
     * If the endpoint URL contains a marker ###FORMAT### or {format}
     * it will be replaced with the expected response data format.
     *
     * @param array $parameters
     * @return string
     */
    protected function buildRequestUrl(array $parameters): string
    {
        if (strstr('?', $this->endpoint)) {
            $firstParameter = false;
        } else {
            $firstParameter = true;
        }

        $requestUrl = $this->endpoint;

        $markerService = GeneralUtility::makeInstance(MarkerBasedTemplateService::class);
        $requestUrl = $markerService->substituteMarker($requestUrl, '###FORMAT###', $this->format);
        $requestUrl = $markerService->substituteMarker($requestUrl, '{format}', $this->format);

        foreach ($parameters as $name => $value) {
            $name = urlencode($name);
            $value = urlencode((string)$value);

            if (!$firstParameter) {
                $parameterGlue = '&';
            } else {
                $parameterGlue = '?';
                $firstParameter = false;
            }

            $requestUrl .= $parameterGlue . $name . '=' . $value;
        }

        return $requestUrl;
    }

    /**
     * Sends a request to the given URL and returns the reponse
     * from the server.
     *
     * @param string $requestUrl
     * @return string response data
     * @throws HttpNotFoundException
     * @throws HttpNotImplementedException
     * @throws HttpUnauthorizedException
     */
    protected function sendRequest($requestUrl): string
    {
        $requestException = null;
        try {
            return $this->getHttpClient()->executeGetRequest($requestUrl);
        } catch (HttpClientRequestException $e) {
            $requestException = $e;
        }

        $mediaUrl = $this->configuration->getMediaUrl();
        switch ($requestException->getCode()) {
            case 404:
                throw new HttpNotFoundException($mediaUrl, $requestUrl);
                break;
            case 501:
                throw new HttpNotImplementedException(
                    $mediaUrl,
                    $this->format,
                    $requestUrl
                );
                break;
            case 401:
                throw new HttpUnauthorizedException($mediaUrl, $requestUrl);
                break;
            default:
                throw new RuntimeException(
                    'An unknown error occurred while contacting the provider: '
                    . $requestException->getMessage() . ' (' . $requestException->getErrorDetails() . ').'
                    . ' Please make sure CURL use is enabled in the install tool to get valid error codes.',
                    1303401545
                );
                break;
        }
    }

    private function getHttpClient(): HttpClientInterface
    {
        return $this->httpClientFactory->getHttpClient();
    }
}
