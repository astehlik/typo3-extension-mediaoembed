<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Content;

class Settings
{
    private array $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function getAspectRatioFallback(): string
    {
        return $this->settings['aspectRatioFallback'] ?? '';
    }

    public function getHttpClientClass(): string
    {
        return $this->settings['httpClient'] ?? '';
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

    public function getProviders(): array
    {
        return $this->settings['providers'] ?? [];
    }

    public function isPhotoDownloadEnabled(): bool
    {
        return (bool)$this->settings['downloadPhoto'];
    }
}
