<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Content;

readonly class Settings
{
    public function __construct(
        private array $settings
    ) {}

    public function getAspectRatioFallback(): string
    {
        return $this->settings['aspectRatioFallback'] ?? '';
    }

    public function getEmbedResponsiveClass(): string
    {
        return $this->settings['embedResponsiveClass'] ?? '';
    }

    public function getEmbedResponsiveStyleProperty(): string
    {
        return $this->settings['embedResponsiveStyleProperty'] ?? '';
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
        return $this->settings['downloadPhotoSettings']['folderIdentifier'] ?? '';
    }

    public function getPhotoDownloadStorageUid(): int
    {
        return (int)($this->settings['downloadPhotoSettings']['storageUid'] ?? 0);
    }

    public function getProcessorsForHtml(): array
    {
        return $this->settings['reponseProcessors']['html'] ?? [];
    }

    public function isConsentEnabled(): bool
    {
        return (bool)($this->settings['consent']['enabled'] ?? false);
    }

    public function isConsentPreviewEnabled(): bool
    {
        return (bool)($this->settings['consent']['showPreview'] ?? false);
    }

    public function isPhotoDownloadEnabled(): bool
    {
        return (bool)($this->settings['downloadPhoto'] ?? false);
    }
}
