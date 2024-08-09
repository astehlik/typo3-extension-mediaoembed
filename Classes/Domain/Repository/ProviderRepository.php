<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Domain\Repository;

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
use Sto\Mediaoembed\Exception\InvalidConfigurationException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Repository for fetching providers from the configuration.
 */
class ProviderRepository
{
    private ConfigurationManagerInterface $configurationManager;

    public function __construct(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @return array|Provider[]
     */
    public function findAll(): array
    {
        $providers = [];
        foreach ($this->getProvidersConfig() as $providerName => $providerConfig) {
            $providers[] = $this->createProvider($providerName, $providerConfig);
        }
        return $providers;
    }

    private function addProcessors(Provider $provider, array $processors): void
    {
        foreach ($processors as $processor) {
            $provider->withProcessor($processor);
        }
    }

    private function createProvider(string $providerName, array $providerConfig): Provider
    {
        $this->validateProviderName($providerName);

        $endpoint = trim((string)($providerConfig['endpoint'] ?? ''));
        $this->validateEndpoint($endpoint, $providerName);

        $urlRegexes = (array)($providerConfig['urlRegexes'] ?? []);
        $urlSchemes = (array)($providerConfig['urlSchemes'] ?? []);
        $this->validateUrlSchemes($urlRegexes, $urlSchemes, $providerName);

        $hasRegexUrlSchemes = count($urlRegexes) > 0;

        $provider = new Provider(
            $providerName,
            $endpoint,
            $hasRegexUrlSchemes ? $urlRegexes : $urlSchemes,
            $hasRegexUrlSchemes,
        );

        if (!empty($providerConfig['requestHandlerClass'])) {
            $provider->withRequestHandler(
                $providerConfig['requestHandlerClass'],
                $providerConfig['requestHandlerSettings'] ?? [],
            );
        }

        if (isset($providerConfig['displayDirectLink']) && !$providerConfig['displayDirectLink']) {
            $provider->hideDirectLink();
        }

        $this->addProcessors($provider, (array)($providerConfig['processors'] ?? []));

        return $provider;
    }

    private function getProvidersConfig(): array
    {
        $settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
        );

        return (array)($settings['providers'] ?? []);
    }

    private function validateEndpoint(string $endpoint, string $providerName): void
    {
        if ($endpoint === '') {
            throw new InvalidConfigurationException(sprintf('Endpoint of provider %s is empty.', $providerName));
        }

        if (!GeneralUtility::isValidUrl($endpoint)) {
            throw new InvalidConfigurationException(
                sprintf('Endpoint of provider %s is an invalid URL: %s', $providerName, $endpoint),
            );
        }
    }

    private function validateProviderName(string $providerName): void
    {
        if ($providerName === '') {
            throw new InvalidConfigurationException('Provider name must not be empty.');
        }
    }

    private function validateUrlSchemes(array $urlRegexes, array $urlSchemes, string $providerName): void
    {
        if (count($urlRegexes) && count($urlSchemes)) {
            throw new InvalidConfigurationException(
                sprintf('A provider can have either urlRegexes or urlSchemes. The provider %s has both.', $providerName),
            );
        }

        if ($urlSchemes === [] && $urlRegexes === []) {
            throw new InvalidConfigurationException(sprintf('The provider %s has no URL schemes.', $providerName));
        }
    }
}
