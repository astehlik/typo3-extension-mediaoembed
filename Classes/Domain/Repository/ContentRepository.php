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
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Repository for mediaoembed tt_content elements.
 */
class ContentRepository implements SingletonInterface
{
    /**
     * @var ObjectManagerInterface
     * */
    protected $objectManager;

    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function buildFromContentObjectData(array $contentObjectData): Content
    {
        $content = $this->objectManager->get(Content::class);

        $content->setMaxHeight((int)$contentObjectData['tx_mediaoembed_maxheight'] ?? 0);
        $content->setMaxWidth((int)$contentObjectData['tx_mediaoembed_maxwidth'] ?? 0);
        $content->setUrl((string)$contentObjectData['tx_mediaoembed_url'] ?? '');

        return $content;
    }
}
