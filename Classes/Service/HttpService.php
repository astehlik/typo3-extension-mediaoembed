<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;

readonly class HttpService
{
    public function __construct(
        private RequestFactoryInterface $requestFactory,
        private ClientInterface $client,
    ) {}

    public function getUrl(string $uri): ResponseInterface
    {
        $req = $this->requestFactory->createRequest('GET', $uri);

        return $this->client->sendRequest($req);
    }
}
