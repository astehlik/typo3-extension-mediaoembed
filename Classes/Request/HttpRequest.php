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
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Represents a HTTP request.
 */
class HttpRequest
{
    /**
     * The configuration.
     */
    private Configuration $configuration;

    /**
     * The endpoint URL that should be contacted to get the embed
     * information.
     */
    private string $endpoint;

    /**
     * The required response format. When not specified, the provider can return
     * any valid response format.
     * When specified, the provider must return data in the request format,
     * else return an error (see below for error codes).
     * This value is optional.
     *
     * Important! At the moment, we only handle JSON formatted Responses.
     */
    private string $format = 'json';

    private HttpClientFactory $httpClientFactory;

    private array $httpErrorHandlers = [
        401,
        404,
        501,
    ];

    public function __construct(Configuration $configuration, string $endpoint, HttpClientFactory $httpClientFactory)
    {
        $this->configuration = $configuration;
        $this->endpoint = $endpoint;
        $this->httpClientFactory = $httpClientFactory;
    }

    /**
     * Builds a request url and tries to read the embed information
     * from the server. Result should be in json format.
     *
     * @return string json formatted result from server
     */
    public function sendAndGetResponseData(): string
    {
        $parameters = $this->buildRequestParameterArray();
        $requestUrl = $this->buildRequestUrl($parameters);
        return $this->sendRequest($requestUrl);
    }

    protected function addRequestParameterFormat(array &$parameters): void
    {
        if (isset($this->format)) {
            $parameters['format'] = $this->format;
        }
    }

    protected function addRequestParameterMaxHeight(array &$parameters): void
    {
        $maxheight = $this->configuration->getMaxheight();
        if ($maxheight > 0) {
            $parameters['maxheight'] = $maxheight;
        }
    }

    protected function addRequestParameterMaxWidth(array &$parameters): void
    {
        $maxwidth = $this->configuration->getMaxwidth();
        if ($maxwidth > 0) {
            $parameters['maxwidth'] = $maxwidth;
        }
    }

    protected function buildQueryStringParameters(string $endpointQueryParameters, array $parameters): array
    {
        $baseUrlParameters = [];
        if ($endpointQueryParameters) {
            parse_str($endpointQueryParameters, $baseUrlParameters);
        }

        $finalParameters = $baseUrlParameters;
        ArrayUtility::mergeRecursiveWithOverrule($finalParameters, $parameters);

        return $finalParameters;
    }

    /**
     * Builds an array of parameters that should be attached to the
     * endpoint url.
     */
    protected function buildRequestParameterArray(): array
    {
        $parameters = [];

        $this->addRequestParameterMaxWidth($parameters);
        $this->addRequestParameterMaxHeight($parameters);
        $this->addRequestParameterFormat($parameters);

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
     */
    protected function buildRequestUrl(array $parameters): string
    {
        $requestUrl = $this->endpoint;
        $requestUrl = $this->replaceFormatPlaceholders($requestUrl);

        $urlParts = explode('?', $requestUrl, 2);
        $endpointBaseUrl = $urlParts[0];
        $endpointQueryParameters = $urlParts[1] ?? '';

        $finalParameters = $this->buildQueryStringParameters($endpointQueryParameters, $parameters);
        if (count($finalParameters) === 0) {
            return $endpointBaseUrl;
        }

        return $this->buildUrlWithQueryString($endpointBaseUrl, $finalParameters);
    }

    protected function buildUrlWithQueryString(string $endpointBaseUrl, array $finalParameters): string
    {
        $queryString = GeneralUtility::implodeArrayForUrl('', $finalParameters);
        $queryString = ltrim($queryString, '&');
        return $endpointBaseUrl . '?' . $queryString;
    }

    protected function handleError401(string $requestUrl): void
    {
        throw new HttpUnauthorizedException($this->configuration->getMediaUrl(), $requestUrl);
    }

    protected function handleError404(string $requestUrl): void
    {
        throw new HttpNotFoundException($this->configuration->getMediaUrl(), $requestUrl);
    }

    protected function handleError501(string $requestUrl): void
    {
        throw new HttpNotImplementedException(
            $this->configuration->getMediaUrl(),
            $this->format,
            $requestUrl,
        );
    }

    protected function handleErrorUnknown(HttpClientRequestException $requestException): void
    {
        throw new RuntimeException(
            'An unknown error occurred while contacting the provider: '
            . $requestException->getMessage() . '.',
            1303401545,
            $requestException,
        );
    }

    protected function handleRequestError(HttpClientRequestException $requestException, string $requestUrl): void
    {
        $errorCode = $requestException->getCode();
        if (!in_array($errorCode, $this->httpErrorHandlers, true)) {
            $this->handleErrorUnknown($requestException);
        }

        /**
         * @uses handleError401()
         * @uses handleError404()
         * @uses handleError501()
         */
        $errorHandlerMethod = 'handleError' . $errorCode;
        $this->{$errorHandlerMethod}($requestUrl);
    }

    /**
     * @return string|string[]
     */
    protected function replaceFormatPlaceholders(string $requestUrl)
    {
        $requestUrl = str_replace('###FORMAT###', $this->format, $requestUrl);
        return str_replace('{format}', $this->format, $requestUrl);
    }

    /**
     * Sends a request to the given URL and returns the reponse
     * from the server.
     *
     * @return string response data
     */
    protected function sendRequest(string $requestUrl): string
    {
        try {
            return $this->getHttpClient()->executeGetRequest($requestUrl);
        } catch (HttpClientRequestException $e) {
            $requestException = $e;
        }

        $this->handleRequestError($requestException, $requestUrl);

        throw new RuntimeException('This step should never be reached!');
    }

    private function getHttpClient(): HttpClientInterface
    {
        return $this->httpClientFactory->getHttpClient($this->configuration);
    }
}
