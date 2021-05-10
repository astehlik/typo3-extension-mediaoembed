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
use Sto\Mediaoembed\Domain\Repository\ContentRepository;
use Sto\Mediaoembed\Service\AspectRatioCalculatorInterface;
use Sto\Mediaoembed\Service\ConfigurationService;

/**
 * Handels TypoScript and content object configuration
 */
class Configuration
{
    const ASPECT_RATIO_DEFAULT = '16:9';

    /**
     * @var AspectRatioCalculatorInterface
     */
    private $aspectRatioCalculator;

    /**
     * @var ConfigurationService
     */
    private $configurationService;

    /**
     * @var ContentRepository
     */
    private $contentRepository;

    public function __construct(
        AspectRatioCalculatorInterface $aspectRatioCalculator,
        ConfigurationService $configurationService,
        ContentRepository $contentRepository
    ) {
        $this->aspectRatioCalculator = $aspectRatioCalculator;
        $this->configurationService = $configurationService;
        $this->contentRepository = $contentRepository;
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

    /**
     * The maximum height of the embedded resource.
     * Only applies to some resource types (as specified below).
     * For supported resource types, this parameter must be respected by providers.
     * This value is optional.
     *
     * @return int
     */
    public function getMaxheight(): int
    {
        $contentMaxHeight = $this->getContent()->getMaxHeight();
        if (!empty($contentMaxHeight)) {
            return $contentMaxHeight;
        }

        return $this->configurationService->getMaxHeight();
    }

    /**
     * The maximum width of the embedded resource.
     * Only applies to some resource types (as specified below).
     * For supported resource types, this parameter must be respected by providers.
     * This value is optional.
     *
     * @return int
     */
    public function getMaxwidth(): int
    {
        $contentMaxWidth = $this->getContent()->getMaxWidth();
        if (!empty($contentMaxWidth)) {
            return $contentMaxWidth;
        }

        return $this->configurationService->getMaxWidth();
    }

    public function getMediaUrl(): string
    {
        return $this->getContent()->getUrl();
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
        $fallbackAspectRatio = $this->calculateAspectRatio($this->configurationService->getAspectRatioFallback());
        if ($fallbackAspectRatio) {
            return $fallbackAspectRatio;
        }

        return $this->calculateAspectRatio(self::ASPECT_RATIO_DEFAULT);
    }

    /**
     * Returns the current tt_content record domain model.
     *
     * @return Content
     */
    private function getContent(): Content
    {
        return $this->contentRepository->getCurrentContent();
    }
}
