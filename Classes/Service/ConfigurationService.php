<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class ConfigurationService
{
    /**
     * @var array
     */
    private $settings = [];

    public function getAspectRatioFallback(): string
    {
        return $this->settings['aspectRatioFallback'] ?? '';
    }

    public function getMaxHeight(): int
    {
        if (!empty($this->settings['media']['maxheight'])) {
            return (int)$this->settings['media']['maxheight'];
        }

        return 0;
    }

    public function getMaxWidth(): int
    {
        if (!empty($this->settings['media']['maxwidth'])) {
            return (int)$this->settings['media']['maxwidth'];
        }

        return 0;
    }

    public function getPhotoDownloadFolderIdentifier(): string
    {
        return $this->settings['downloadPhotoSettings']['folderIdentifier'];
    }

    public function getPhotoDownloadStorageUid(): int
    {
        return (int)$this->settings['downloadPhotoSettings']['storageUid'];
    }

    public function getProcessorsForHtml(): array
    {
        return $this->settings['reponseProcessors']['html'] ?? [];
    }

    public function injectConfigurationManager(ConfigurationManagerInterface $configurationManager): void
    {
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'Mediaoembed'
        );
    }

    public function isPhotoDownloadEnabled(): bool
    {
        return (bool)$this->settings['downloadPhoto'];
    }
}
