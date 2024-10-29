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

/**
 * Repository for mediaoembed tt_content elements.
 */
class ContentRepository
{
    public function createFromContentData(array $contentObjectData): Content
    {
        return new Content(
            (int)($contentObjectData['uid'] ?? 0),
            (string)($contentObjectData['tx_mediaoembed_url'] ?? ''),
            (int)($contentObjectData['tx_mediaoembed_maxheight'] ?? 0),
            (int)($contentObjectData['tx_mediaoembed_maxwidth'] ?? 0),
            (bool)($contentObjectData['tx_mediaoembed_play_related'] ?? true),
            (string)($contentObjectData['tx_mediaoembed_aspect_ratio'] ?? ''),
        );
    }
}
