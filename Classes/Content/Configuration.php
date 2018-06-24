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
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Handels TypoScript and content object configuration
 */
class Configuration
{
    /**
     * @var ContentRepository
     */
    private $contentRepository;

    /**
     * Current TypoScript / Flexform configuration
     *
     * @var array
     */
    private $settings;

    public function __construct(
        ConfigurationManagerInterface $configurationManager,
        ContentRepository $contentRepository
    ) {
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );
        $this->contentRepository = $contentRepository;
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

        if (!empty($this->settings['media']['maxheight'])) {
            return (int)$this->settings['media']['maxheight'];
        }

        return 0;
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

        if (!empty($this->settings['media']['maxwidth'])) {
            return (int)$this->settings['media']['maxwidth'];
        }

        return 0;
    }

    public function getMediaUrl(): string
    {
        return $this->getContent()->getUrl();
    }

    /**
     * Returns the current tt_content record domain model.
     *
     * @return \Sto\Mediaoembed\Domain\Model\Content
     */
    private function getContent()
    {
        return $this->contentRepository->getCurrentContent();
    }
}
