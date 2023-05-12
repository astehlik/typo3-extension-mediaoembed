<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Response\Processor\YouTube;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Response\Processor\ResponseProcessorInterface;
use Sto\Mediaoembed\Response\Processor\Support\IframeAwareProcessorTrait;
use Sto\Mediaoembed\Response\VideoResponse;
use Sto\Mediaoembed\Service\UrlService;

class PlayRelatedProcessor implements ResponseProcessorInterface
{
    use IframeAwareProcessorTrait;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var UrlService
     */
    private $urlService;

    public function __construct(Configuration $configuration, UrlService $urlService)
    {
        $this->configuration = $configuration;
        $this->urlService = $urlService;
    }

    public function processResponse(GenericResponse $response): void
    {
        if (!$response instanceof VideoResponse) {
            throw new \InvalidArgumentException('This processor only works with video responses!');
        }

        $this->processVideoResponse($response);
    }

    private function processVideoResponse(VideoResponse $response): void
    {
        $replaceYoutubeUrl = function (string $url) {
            $queryParams['rel'] = $this->configuration->shouldPlayRelated() ? '1' : '0';
            return $this->urlService->mergeQueryParameters($url, $queryParams);
        };

        $this->modifyIframeUrl($response, $replaceYoutubeUrl);
    }
}
