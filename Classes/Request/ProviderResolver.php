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
use Sto\Mediaoembed\Exception\InvalidUrlException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Resolves a matching provider for the given URL
 */
class ProviderResolver
{
    /**
     * @var \Sto\Mediaoembed\Domain\Repository\ProviderRepository
     * */
    protected $providerRepository;

    /**
     * The SQL result of the provider query, should not contain
     * all active, non generic providers.
     *
     * @var \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    protected $providerResult;

    /**
     * Contains the current media URL
     *
     * @var string
     */
    protected $url;

    public function injectProviderRepository(\Sto\Mediaoembed\Domain\Repository\ProviderRepository $providerRepository)
    {
        $this->providerRepository = $providerRepository;
    }

    /**
     * Returns the next active provider whos url scheme matches the URL in
     * the current configuration
     *
     * @param \Sto\Mediaoembed\Domain\Model\Content $content
     * @return Provider The next matching provider
     */
    public function getNextMatchingProvider($content)
    {
        $this->url = $content->getUrl();
        $this->checkIfUrlIsValid();

        $this->providerResult = $this->providerRepository->findByIsGeneric(false);
        $this->providerResult->rewind();

        $provider = $this->detectNextMatchingProvider();

        return $provider;
    }

    /**
     * Checks if the current URL is valid
     *
     * @return void
     * @throws InvalidUrlException
     */
    protected function checkIfUrlIsValid()
    {
        $isValid = true;

        if (empty($this->url)) {
            $isValid = false;
        }

        if (!GeneralUtility::isValidUrl($this->url)) {
            $isValid = false;
        }

        if (!$isValid) {
            throw new InvalidUrlException($this->url);
        }
    }

    /**
     * Searches for a url scheme that matches the given url. If
     * there is a result, the data of the matching provider will be returned.
     *
     * @return Provider
     * @throws \Sto\Mediaoembed\Exception\NoMatchingProviderException
     */
    protected function detectNextMatchingProvider()
    {
        $matchingProvider = false;

        do {
            $currentProvider = $this->providerResult->current();
            if (!$currentProvider instanceof Provider) {
                break;
            }

            // We don't care about providers that don't have a url scheme
            $urlSchemes = $currentProvider->getUrlSchemes();
            if (empty($urlSchemes)) {
                continue;
            }

            $urlSchemes = GeneralUtility::trimExplode(LF, $urlSchemes);

            foreach ($urlSchemes as $urlScheme) {
                if ($urlScheme === '') {
                    continue;
                }
                $urlScheme = preg_quote($urlScheme, '/');
                $urlScheme = str_replace('\*', '.*', $urlScheme);
                if (preg_match('/' . $urlScheme . '/', $this->url)) {
                    $matchingProvider = $currentProvider;
                    break 2;
                }
            }

            $this->providerResult->next();
        } while ($matchingProvider === false);

        if ($matchingProvider === false) {
            throw new \Sto\Mediaoembed\Exception\NoMatchingProviderException($this->url);
        }

        return $matchingProvider;
    }
}
