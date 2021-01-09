<?php

namespace Sto\Mediaoembed\Response\Processor\YouTube;

use InvalidArgumentException;
use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Response\Processor\ResponseProcessorInterface;
use Sto\Mediaoembed\Response\Processor\Support\IframeAwareProcessorTrait;
use Sto\Mediaoembed\Response\VideoResponse;

class NocookieProcessor implements ResponseProcessorInterface
{
    use IframeAwareProcessorTrait;

    public function processResponse(GenericResponse $response)
    {
        if (!$response instanceof VideoResponse) {
            throw new InvalidArgumentException('This processor only works with video responses!');
        }

        $this->processVideoResponse($response);
    }

    private function processVideoResponse(VideoResponse $response)
    {
        $replaceYoutubeUrl = function (array $urlParts) {
            $newUrl = 'https://www.youtube-nocookie.com';
            $newUrl .= ($urlParts['path'] ?? '');

            $query = ($urlParts['query'] ?? '');
            if ($query) {
                $newUrl .= '?' . $query;
            }

            return $newUrl;
        };

        $this->modifyIframeUrl($response, $replaceYoutubeUrl);
    }
}
