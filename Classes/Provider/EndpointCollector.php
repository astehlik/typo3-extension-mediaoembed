<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Provider;

class EndpointCollector
{
    private ProviderEndpoints $providerEndpoints;

    private ProviderUrls $providerUrls;

    public function __construct(
        ProviderEndpoints $providerEndpoints,
        ProviderUrls $providerUrls
    ) {
        $this->providerEndpoints = $providerEndpoints;
        $this->providerUrls = $providerUrls;
    }

    /**
     * @return array|Endpoint[]
     */
    public function collectEndpoints(): array
    {
        $this->checkForDuplicateEndpointLabels();
        $this->checkForMissingEndpointLabels();

        $endpointLabels = $this->getEndpointLabels();
        $endpointsByName = [];

        foreach ($this->getProviderData() as $urlScheme => $providerData) {
            list($endpointUrl, $isRegex) = $providerData;
            $endpointLabel = $endpointLabels[$endpointUrl];

            $endpoint = $this->getOrCreateEndpoint($endpointsByName, $endpointLabel, $endpointUrl, $isRegex);
            $endpoint->addUrlScheme($urlScheme);
        }

        ksort($endpointsByName);

        return $endpointsByName;
    }

    private function checkForDuplicateEndpointLabels(): void
    {
        $checkedLabels = [];
        foreach ($this->getEndpointLabels() as $label) {
            if (array_key_exists($label, $checkedLabels)) {
                throw new \RuntimeException('Duplicate endpoint label ' . $label);
            }
            $checkedLabels[$label] = true;
        }
    }

    private function checkForMissingEndpointLabels(): void
    {
        $endpointLabels = $this->getEndpointLabels();

        foreach ($this->getProviderData() as $providerData) {
            $endpointUrl = $providerData[0];
            if (!array_key_exists($endpointUrl, $endpointLabels)) {
                throw new \RuntimeException('No label configured for endpoint URL ' . $endpointUrl);
            }
        }
    }

    private function getEndpointLabels(): array
    {
        return $this->providerEndpoints->getEndpoints();
    }

    private function getOrCreateEndpoint(
        array &$endpointsByName,
        string $endpointName,
        string $endpointUrl,
        bool $isRegex
    ): Endpoint {
        if (isset($endpointsByName[$endpointName])) {
            return $endpointsByName[$endpointName];
        }

        $endpoint = new Endpoint($endpointName, $endpointUrl, $isRegex);
        $endpointsByName[$endpointName] = $endpoint;
        return $endpoint;
    }

    /**
     * This data is based on the data from the WordPress class WP_oEmbed.
     */
    private function getProviderData(): array
    {
        return $this->providerUrls->getUrls();
    }
}
