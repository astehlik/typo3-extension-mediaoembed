<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Response\Processor\YouTube;

use InvalidArgumentException;
use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Response\Processor\ResponseProcessorInterface;
use Sto\Mediaoembed\Response\Processor\Support\IframeManipulator;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Service\UrlService;
use TYPO3\CMS\Core\Http\Uri;

readonly class NocookieProcessor implements ResponseProcessorInterface
{
    public function __construct(
        private IframeManipulator $iframeManipulator,
        private UrlService $urlService
    ) {}

    public function processResponse(GenericResponse $response): void
    {
        if (!$response instanceof VideoResponse) {
            throw new InvalidArgumentException('This processor only works with video responses!');
        }

        $this->processVideoResponse($response);
    }

    private function processVideoResponse(VideoResponse $response): void
    {
        $replaceYoutubeUrl = fn(?Uri $url): ?Uri => $this->replaceYoutubeUrl($url);

        $this->iframeManipulator->modifyIframeUrl($response, $replaceYoutubeUrl);
    }

    private function replaceYoutubeUrl(?Uri $url): ?Uri
    {
        if (!$url instanceof Uri) {
            return null;
        }

        if ($url->getHost() === '') {
            return $url;
        }

        return $this->urlService->replaceSchemeAndHost(
            $url,
            'https',
            'www.youtube-nocookie.com'
        );
    }
}
