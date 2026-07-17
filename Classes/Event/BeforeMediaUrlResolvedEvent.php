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
 * used for provider resolving, so listeners can rewrite it.
 *
 * The event carries two independent URLs, both initialized to the raw media
 * URL:
 *
 * - `url` is shown to visitors (direct link, consent placeholder text) and
 *   kept as entered by editors unless a listener decides otherwise.
 * - `requestUrl` is used for provider resolving and the request to the
 *   provider, and can be rewritten into a form that a provider's URL schemes
 *   will match without affecting what is displayed.
 */
final class BeforeMediaUrlResolvedEvent
{
    private string $requestUrl;

    public function __construct(private string $url)
    {
        $this->requestUrl = $url;
    }

    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setRequestUrl(string $requestUrl): void
    {
        $this->requestUrl = $requestUrl;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
