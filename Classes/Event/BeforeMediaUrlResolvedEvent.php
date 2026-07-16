<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Event;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * Dispatched with the raw media URL from the content element before it is
 * used for provider resolving, so listeners can rewrite it into a form
 * that a provider's URL schemes will match.
 */
final class BeforeMediaUrlResolvedEvent
{
    public function __construct(private string $url) {}

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
