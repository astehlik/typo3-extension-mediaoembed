<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use Sto\Mediaoembed\Exception\InvalidUrlException;
use TYPO3\CMS\Core\Utility\ArrayUtility;

final class UrlService
{
    /**
     * Takes query parts and builds a URL. Based on:
     * https://stackoverflow.com/a/31691249
     *
     * @param array $urlParts
     * @param string $originalUrl
     * @return string
     */
    public function buildUrl(array $urlParts, string $originalUrl): string
    {
        $hasFragment = strpos($originalUrl, '#') !== false;
        $hasQuery = strpos($originalUrl, '?') !== false;

        $pass = $urlParts['pass'] ?? null;
        $user = $urlParts['user'] ?? null;
        $userinfo = $pass !== null ? $user . ':' . $pass : $user;
        $port = $urlParts['port'] ?? 0;
        $scheme = $urlParts['scheme'] ?? '';
        $query = $urlParts['query'] ?? '';
        $fragment = $urlParts['fragment'] ?? '';

        $authority = ($userinfo !== null ? $userinfo . '@' : '')
            . ($urlParts['host'] ?? "")
            . ($port ? ':' . $port : '');

        return ($scheme ? $scheme . ':' : '')
            . ($authority ? '//' . $authority : '')
            . ($urlParts['path'] ?? '')
            . ($hasQuery ? '?' . $query : '')
            . ($hasFragment ? '#' . $fragment : '');
    }

    public function mergeQueryParameters(string $url, array $parameters): string
    {
        if ($parameters === []) {
            return $url;
        }

        $urlParts = $this->parseUrl($url);

        $query = $urlParts['query'] ?? '';
        $newQuery = $this->queryParamsOverwrite($query, $parameters);
        $urlParts['query'] = $newQuery;

        return $this->buildUrl($urlParts, $url);
    }

    public function parseUrl(string $url): array
    {
        $urlParts = parse_url($url);
        if (!$urlParts) {
            throw new InvalidUrlException($url);
        }
        return $urlParts;
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

    public function replaceSchemeAndHost(string $url, string $scheme, string $host): string
    {
        $urlParts = $this->parseUrl($url);
        $urlParts['scheme'] = $scheme;
        $urlParts['host'] = $host;
        return $this->buildUrl($urlParts, $url);
    }

    private function parseQueryParams(string $query): array
    {
        $queryParams = [];
        if ($query) {
            parse_str($query, $queryParams);
        }
        return $queryParams;
    }
}
