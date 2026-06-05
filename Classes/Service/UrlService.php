<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use InvalidArgumentException;
use Sto\Mediaoembed\Exception\InvalidUrlException;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Utility\ArrayUtility;

final class UrlService
{
    public function mergeQueryParameters(Uri $url, array $parameters): Uri
    {
        if ($parameters === []) {
            return $url;
        }

        $newQuery = $this->queryParamsOverwrite($url->getQuery(), $parameters);

        return $url->withQuery($newQuery);
    }

    public function parseUrl(string $url): Uri
    {
        try {
            return new Uri($url);
        } catch (InvalidArgumentException $e) {
            throw new InvalidUrlException($url, $e);
        }
    }

    public function queryParamsDefaults(string $query, array $defaultParameters): string
    {
        if ($defaultParameters === []) {
            return $query;
        }
        $queryParams = $this->parseQueryParams($query);
        ArrayUtility::mergeRecursiveWithOverrule($defaultParameters, $queryParams);
        return http_build_query($defaultParameters);
    }

    public function queryParamsOverwrite(string $query, array $overwriteParameters): string
    {
        if ($overwriteParameters === []) {
            return $query;
        }
        $queryParams = $this->parseQueryParams($query);
        ArrayUtility::mergeRecursiveWithOverrule($queryParams, $overwriteParameters);
        return http_build_query($queryParams);
    }

    public function replaceSchemeAndHost(Uri $url, string $scheme, string $host): Uri
    {
        return $url->withScheme($scheme)
            ->withHost($host);
    }

    private function parseQueryParams(string $query): array
    {
        $queryParams = [];
        if ($query !== '' && $query !== '0') {
            parse_str($query, $queryParams);
        }
        return $queryParams;
    }
}
