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
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Repository for fetching providers from the configuration.
 */
class ProviderRepository implements SingletonInterface
{
    /**
     * @var array
     */
    private $providersConfig;

    public function __construct(ConfigurationManagerInterface $configurationManager)
    {
        $settings = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
        $this->providersConfig = (array)$settings['providers'] ?? [];
    }

    /**
     * @return Provider[]|array
     */
    public function findAll(): array
    {
        $providers = [];
        foreach ($this->providersConfig as $providerName => $providerConfig) {
            $providers[] = $this->createProvider($providerName, $providerConfig);
        }
        return $providers;
    }

    private function createProvider(string $providerName, array $providerConfig): Provider
    {
        if ($providerName === '') {
            throw new InvalidConfigurationException('Provider name must not be empty');
        }

        $endpoint = trim($providerConfig['endpoint']);

        if ($endpoint === '') {
            throw new InvalidConfigurationException(sprintf('Endpoint of provider %s is empty.', $providerName));
        }

        if (!GeneralUtility::isValidUrl($endpoint)) {
            throw new InvalidConfigurationException(
                sprintf('Endpoint of provider %s is an invalid URL.', $providerName)
            );
        }

        $hasRegexUrlSchemes = true;
        $urlSchemes = (array)$providerConfig['urlRegexes'] ?? [];
        if ($urlSchemes === []) {
            $urlSchemes = (array)$providerConfig['urlSchemes'] ?? [];
            $hasRegexUrlSchemes = false;
        }

        if ($hasRegexUrlSchemes && !empty($providerConfig['urlSchemes'])) {
            throw new InvalidConfigurationException(
                sprintf('A provider can have either urlRegexes or urlSchemes. The provider %s has both.', $providerName)
            );
        }

        if ($urlSchemes === []) {
            throw new InvalidConfigurationException(sprintf('The provider %s has no URL schemes.', $providerName));
        }

        $provider = new Provider(
            $providerName,
            $endpoint,
            $urlSchemes,
            $hasRegexUrlSchemes
        );

        $processors = (array)$providerConfig['processors'] ?? [];
        if ($processors !== []) {
            foreach ($processors as $processor) {
                $provider->withProcessor($processor);
            }
        }

        return $provider;
    }
}
