<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Content;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Sto\Mediaoembed\Domain\Model\Content;
use Sto\Mediaoembed\Service\AspectRatioCalculatorInterface;

/**
 * Handels TypoScript and content object configuration.
 */
class Configuration
{
    public const ASPECT_RATIO_DEFAULT = '16:9';

    private AspectRatioCalculatorInterface $aspectRatioCalculator;

    private Content $contentElement;

    private Settings $settings;

    public function __construct(
        Content $content,
        Settings $settings,
        AspectRatioCalculatorInterface $aspectRatioCalculator,
    ) {
        $this->contentElement = $content;
        $this->settings = $settings;

        $this->aspectRatioCalculator = $aspectRatioCalculator;
    }

    public function getAspectRatio(float $responseAspectRatio): float
    {
        $overrideAspectRatio = $this->calculateAspectRatio($this->getContent()->getAspectRatio());
        if ($overrideAspectRatio) {
            return $overrideAspectRatio;
        }

        if ($responseAspectRatio) {
            return $responseAspectRatio;
        }

        return $this->getAspectRatioFallback();
    }

    public function getHttpClientClass(): string
    {
        return $this->settings->getHttpClientClass();
    }

    /**
     * The maximum height of the embedded resource.
     * Only applies to some resource types (as specified below).
     * For supported resource types, this parameter must be respected by providers.
     * This value is optional.
     */
    public function getMaxheight(): int
    {
        $contentMaxHeight = $this->getContent()->getMaxHeight();
        if (!empty($contentMaxHeight)) {
            return $contentMaxHeight;
        }

        return $this->settings->getMaxHeight();
    }

    /**
     * The maximum width of the embedded resource.
     * Only applies to some resource types (as specified below).
     * For supported resource types, this parameter must be respected by providers.
     * This value is optional.
     */
    public function getMaxwidth(): int
    {
        $contentMaxWidth = $this->getContent()->getMaxWidth();
        if (!empty($contentMaxWidth)) {
            return $contentMaxWidth;
        }

        return $this->settings->getMaxWidth();
    }

    public function getMediaUrl(): string
    {
        return $this->getContent()->getUrl();
    }

    public function getPhotoDownloadFolderIdentifier(): string
    {
        return $this->settings->getPhotoDownloadFolderIdentifier();
    }

    public function getPhotoDownloadStorageUid(): int
    {
        return $this->settings->getPhotoDownloadStorageUid();
    }

    public function getProcessorsForHtml(): array
    {
        return $this->settings->getProcessorsForHtml();
    }

    public function isPhotoDownloadEnabled(): bool
    {
        return $this->settings->isPhotoDownloadEnabled();
    }

    public function shouldPlayRelated(): bool
    {
        return $this->getContent()->shouldPlayRelated();
    }

    private function calculateAspectRatio(string $aspectRatio): float
    {
        return $this->aspectRatioCalculator->calculateAspectRatio($aspectRatio);
    }

    private function getAspectRatioFallback(): float
    {
        $fallbackAspectRatio = $this->calculateAspectRatio($this->settings->getAspectRatioFallback());
        if ($fallbackAspectRatio) {
            return $fallbackAspectRatio;
        }

        return $this->calculateAspectRatio(self::ASPECT_RATIO_DEFAULT);
    }

    /**
     * Returns the current tt_content record domain model.
     */
    private function getContent(): Content
    {
        return $this->contentElement;
    }
}
