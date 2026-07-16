<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\EventListener\YouTube;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Sto\Mediaoembed\Event\BeforeMediaUrlResolvedEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

/**
 * Rewrites YouTube Shorts URLs to regular watch URLs so that they match
 * the youtube provider's URL schemes.
 */
#[AsEventListener(identifier: 'mediaoembed/youtube-short-url')]
final class YouTubeShortUrlListener
{
    private const URL_PATTERN = '~^https?://((m|www)\\.)?youtube\\.com/shorts/([^/?#]+)~i';

    public function __invoke(BeforeMediaUrlResolvedEvent $event): void
    {
        if (!preg_match(self::URL_PATTERN, $event->getUrl(), $matches)) {
            return;
        }

        $event->setUrl('https://www.youtube.com/watch?v=' . $matches[3]);
    }
}
