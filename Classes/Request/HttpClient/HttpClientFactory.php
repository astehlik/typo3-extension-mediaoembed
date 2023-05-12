<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Request\HttpClient;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

class HttpClientFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $settings;

    public function getHttpClient(): HttpClientInterface
    {
        $httpClientClass = (string)($this->settings['httpClient'] ?? '');
        if ($httpClientClass === '') {
            // If nothing is configured we fallback to our default client.
            $httpClientClass = GetUrlHttpClient::class;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->objectManager->get($httpClientClass);
    }

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );
    }

    public function injectObjectManager(ObjectManagerInterface $objectManager): void
    {
        $this->objectManager = $objectManager;
    }
}
