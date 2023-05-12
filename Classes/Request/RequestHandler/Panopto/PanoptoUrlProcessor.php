<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Request\RequestHandler\Panopto;

use Sto\Mediaoembed\Service\UrlService;
use TYPO3\CMS\Core\Utility\StringUtility;

final class PanoptoUrlProcessor
{
    /**
     * @var UrlService
     */
    private $urlService;

    public function __construct(UrlService $urlService)
    {
        $this->urlService = $urlService;
    }

    public function processUrl(string $mediaUrl, array $defaultViewerUrlParameters): string
    {
        $urlParts = $this->urlService->parseUrl($mediaUrl);
        $urlPath = $urlParts['path'] ?? '';
        $urlQuery = $urlParts['query'] ?? '';

        // URL is already an embed URL so we assume it contains all required parameters.
        if (!StringUtility::endsWith($urlPath, 'Viewer.aspx')) {
            return $mediaUrl;
        }

        $urlParts['path'] = preg_replace('/Viewer.aspx$/', 'Embed.aspx', $urlPath);
        $urlParts['query'] = $this->urlService->queryParamsDefaults($urlQuery, $defaultViewerUrlParameters);

        return $this->urlService->buildUrl($urlParts, $mediaUrl);
    }
}
