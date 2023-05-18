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

use Psr\Http\Message\ServerRequestInterface;
use Sto\Mediaoembed\Domain\Model\Content;
use Sto\Mediaoembed\Domain\Repository\ContentRepository;
use Sto\Mediaoembed\Service\AspectRatioCalculatorInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class ConfigurationFactory
{
    public function __construct(
        private readonly AspectRatioCalculatorInterface $aspectRatioCalculator,
        private readonly ConfigurationManagerInterface $configurationManager,
        private readonly ContentRepository $contentRepository
    ) {
    }

    public function createConfiguration(array $contentObjectData, array $settings): Configuration
    {
        $content = $this->contentRepository->createFromContentData($contentObjectData);
        return $this->createForContent($content, $settings);
    }

    public function createForUrl(string $url): Configuration
    {
        /** @var ServerRequestInterface $request */
        $request = $GLOBALS['TYPO3_REQUEST'];

        // This is a workaround to make sure the ID in the file module is not used as a page id in
        // the Backend configuration manager. Otherwise we get an Exception.
        /** @see \TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager::getCurrentPageIdFromRequest() */
        $this->configurationManager->setRequest($request->withQueryParams([]));

        $settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'Mediaoembed'
        );

        $this->configurationManager->setRequest($request);

        return new Configuration(
            new Content(0, $url),
            new Settings($settings),
            $this->aspectRatioCalculator
        );
    }

    private function createForContent(Content $content, array $settings): Configuration
    {
        $settings = new Settings($settings);

        return new Configuration($content, $settings, $this->aspectRatioCalculator);
    }
}
