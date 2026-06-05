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

readonly class PlayRelatedProcessor implements ResponseProcessorInterface
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

    private function modifyIframeUrl(?Uri $url, VideoResponse $response): ?Uri
    {
        if (!$url instanceof Uri) {
            return null;
        }

        $queryParams['rel'] = $response->getConfiguration()->shouldPlayRelated() ? '1' : '0';

        return $this->urlService->mergeQueryParameters($url, $queryParams);
    }

    private function processVideoResponse(VideoResponse $response): void
    {
        $replaceYoutubeUrl = fn(?Uri $url): ?Uri => $this->modifyIframeUrl($url, $response);

        $this->iframeManipulator->modifyIframeUrl($response, $replaceYoutubeUrl);
    }
}
