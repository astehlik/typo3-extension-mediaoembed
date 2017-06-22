<?php

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

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Handels TypoScript and FlexForm configuration
 */
class Configuration
{
    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     * @inject
     */
    protected $configurationManager;

    /**
     * @var \Sto\Mediaoembed\Domain\Model\Content
     */
    protected $content;

    /**
     * @var \Sto\Mediaoembed\Domain\Repository\ContentRepository
     * @inject
     */
    protected $contentRepository;

    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Current TypoScript / Flexform configuration
     *
     * @var array
     */
    protected $settings;

    /**
     * Initialzes required instance variables after all injects were made.
     */
    public function initializeObject()
    {
        $this->content = $this->contentRepository->findByUid(
            $this->configurationManager->getContentObject()->data['uid']
        );
        $this->settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );
    }

    /**
     * Returns the current tt_content record domain model.
     *
     * @return \Sto\Mediaoembed\Domain\Model\Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * The maximum height of the embedded resource.
     * Only applies to some resource types (as specified below).
     * For supported resource types, this parameter must be respected by providers.
     * This value is optional.
     *
     * @return int
     */
    public function getMaxheight()
    {
        $maxheight = 0;
        $contentMaxHeight = $this->content->getMaxHeight();

        if (!empty($contentMaxHeight)) {
            $maxheight = $contentMaxHeight;
        } elseif (!empty($this->settings['media']['maxheight'])) {
            $maxheight = (int)$this->settings['media']['maxheight'];
        }

        return $maxheight;
    }

    /**
     * The maximum width of the embedded resource.
     * Only applies to some resource types (as specified below).
     * For supported resource types, this parameter must be respected by providers.
     * This value is optional.
     *
     * @return int
     */
    public function getMaxwidth()
    {
        $maxwidth = 0;
        $contentMaxWidth = $this->content->getMaxWidth();

        if (!empty($contentMaxWidth)) {
            $maxwidth = $contentMaxWidth;
        } elseif (!empty($this->settings['media']['maxwidth'])) {
            $maxwidth = (int)$this->settings['media']['maxwidth'];
        }

        return $maxwidth;
    }
}
