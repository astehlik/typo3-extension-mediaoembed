<?php

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

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager)
    {
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );
    }

    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

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
}
