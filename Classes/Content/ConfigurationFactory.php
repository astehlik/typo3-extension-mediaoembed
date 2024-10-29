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

use Sto\Mediaoembed\Domain\Repository\ContentRepository;
use Sto\Mediaoembed\Service\AspectRatioCalculatorInterface;

class ConfigurationFactory
{
    private AspectRatioCalculatorInterface $aspectRatioCalculator;

    private ContentRepository $contentRepository;

    public function __construct(
        AspectRatioCalculatorInterface $aspectRatioCalculator,
        ContentRepository $contentRepository,
    ) {
        $this->aspectRatioCalculator = $aspectRatioCalculator;
        $this->contentRepository = $contentRepository;
    }

    public function createConfiguration(array $contentObjectData, array $settings): Configuration
    {
        $content = $this->contentRepository->createFromContentData($contentObjectData);
        $settings = new Settings($settings);

        return new Configuration($content, $settings, $this->aspectRatioCalculator);
    }
}
