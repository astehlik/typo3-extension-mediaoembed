<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Request;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "mediaoembed".              *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Exception\NoMatchingProviderException;

/**
 * Resolves a matching provider for the given URL.
 */
class ProviderResolver
{
    /**
     * @var array|Provider[]
     */
    protected array $providerList;

    public function __construct(array $providerList)
    {
        $this->providerList = $providerList;
        reset($this->providerList);
    }

    /**
     * Returns the next active provider of which the url scheme matches the URL in
     * the current configuration.
     *
     * @return Provider The next matching provider
     *
     * @throws NoMatchingProviderException
     */
    public function getNextMatchingProvider(string $url): Provider
    {
        while ($provider = current($this->providerList)) {
            next($this->providerList);
            if ($this->isResponsibleForUrl($provider, $url)) {
                return $provider;
            }
        }

        throw new NoMatchingProviderException($url);
    }

    private function convertUrlSchemeToRegex(string $urlScheme): string
    {
        $urlSchemeWithWildcard = str_replace('*', '___wildcard___', $urlScheme);
        $urlSchemeWithWildcard = preg_quote($urlSchemeWithWildcard, '#');
        $regexedUrlScheme = str_replace('___wildcard___', '(.+)', $urlSchemeWithWildcard);
        $urlRegex = '#' . $regexedUrlScheme . '#i';
        return preg_replace('|^#http\\\\://|', '#https?\\://', $urlRegex);
    }

    private function isResponsibleForUrl(Provider $provider, string $url): bool
    {
        foreach ($provider->getUrlSchemes() as $urlScheme) {
            $urlRegex = $urlScheme;

            if (!$provider->hasRegexUrlSchemes()) {
                $urlRegex = $this->convertUrlSchemeToRegex($urlScheme);
            }

            if (preg_match($urlRegex, $url)) {
                return true;
            }
        }

        return false;
    }
}
