<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Request\HttpClient;

use Psr\Container\ContainerInterface;
use Sto\Mediaoembed\Content\Configuration;

class HttpClientFactory
{
    private ContainerInterface $container;

    public function __construct(
        ContainerInterface $container,
    ) {
        $this->container = $container;
    }

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
