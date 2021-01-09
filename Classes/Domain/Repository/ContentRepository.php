<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Domain\Repository;

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
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Repository for mediaoembed tt_content elements.
 */
class ContentRepository implements SingletonInterface
{
    /**
     * @var ConfigurationManagerInterface
     */
    private $configurationManager;

    public function __construct(ConfigurationManagerInterface $configurationManager)
    {
        $this->configurationManager = $configurationManager;
    }

    public function getCurrentContent(): Content
    {
        // We must rebuild the content object because it might have changed when the plugin
        // is added multiple sites on one page.
        $contentObjectData = $this->configurationManager->getContentObject()->data;

        return new Content(
            (int)$contentObjectData['uid'],
            (string)$contentObjectData['tx_mediaoembed_url'],
            (int)$contentObjectData['tx_mediaoembed_maxheight'],
            (int)$contentObjectData['tx_mediaoembed_maxwidth'],
            (bool)$contentObjectData['tx_mediaoembed_play_related']
        );
    }
}
