<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Request\HttpClient;

use Psr\Container\ContainerInterface;
use Sto\Mediaoembed\Content\Configuration;

readonly class HttpClientFactory
{
    public function __construct(
        private ContainerInterface $container
    ) {}

    public function getHttpClient(Configuration $configuration): HttpClientInterface
    {
        $httpClientClass = $configuration->getHttpClientClass();

        if ($httpClientClass === '') {
            // If nothing is configured we fallback to our default client.
            $httpClientClass = GetUrlHttpClient::class;
        }

        return $this->container->get($httpClientClass);
    }
}
