<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Request\RequestHandler;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Exception\InvalidResponseException;
use Sto\Mediaoembed\Request\HttpClient\HttpClientFactory;
use Sto\Mediaoembed\Request\HttpRequest;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

final class HttpRequestHandler implements RequestHandlerInterface
{
    private HttpClientFactory $httpClientFactory;

    public function __construct(HttpClientFactory $httpClientFactory)
    {
        $this->httpClientFactory = $httpClientFactory;
    }

    public function handle(Provider $provider, Configuration $configuration): array
    {
        $request = new HttpRequest($configuration, $provider->getEndpoint(), $this->httpClientFactory);
        $responseData = $request->sendAndGetResponseData();

        return $this->parseResponseData($responseData);
    }

    private function parseResponseData(string $responseData): array
    {
        $parsedResponseData = json_decode($responseData, true);

        if ($parsedResponseData === null) {
            throw new InvalidResponseException($responseData);
        }

        return $parsedResponseData;
    }
}
