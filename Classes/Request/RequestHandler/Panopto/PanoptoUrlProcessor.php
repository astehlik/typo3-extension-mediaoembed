<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Request\RequestHandler\Panopto;

use Sto\Mediaoembed\Service\UrlService;

final readonly class PanoptoUrlProcessor
{
    public function __construct(private UrlService $urlService) {}

    public function processUrl(string $mediaUrl, array $defaultViewerUrlParameters): string
    {
        $uri = $this->urlService->parseUrl($mediaUrl);

        // URL is already an embed URL so we assume it contains all required parameters.
        if (!str_ends_with($uri->getPath(), 'Viewer.aspx')) {
            return $mediaUrl;
        }

        $newPath = preg_replace('/Viewer.aspx$/', 'Embed.aspx', $uri->getPath());
        $newQuery = $this->urlService->queryParamsDefaults($uri->getQuery(), $defaultViewerUrlParameters);

        return (string)$uri
            ->withPath($newPath)
            ->withQuery($newQuery);
    }
}
