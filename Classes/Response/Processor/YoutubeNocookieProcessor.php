<?php

namespace Sto\Mediaoembed\Response\Processor;

use DOMDocument;
use DOMElement;
use InvalidArgumentException;
use Sto\Mediaoembed\Exception\ProcessorException;
use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Response\VideoResponse;

class YoutubeNocookieProcessor implements ResponseProcessorInterface
{
    public function processResponse(GenericResponse $response): void
    {
        if (!$response instanceof VideoResponse) {
            throw new InvalidArgumentException('This processor only works with video responses!');
        }

        $this->processVideoResponse($response);
    }

    private function processVideoResponse(VideoResponse $response)
    {
        $html = $response->getHtml();
        $htmlWrapping = '<html><body><div id="oembed-response">%s</div></body></html>';
        $document = new DOMDocument();
        $loadSuccess = $document->loadHTML(sprintf($htmlWrapping, $html));
        if (!$loadSuccess) {
            throw new ProcessorException('Error parsing HTML from YouTube response.');
        }

        /** @var DOMElement $iframe */
        $iframe = $document->getElementById('oembed-response')->childNodes->item(0);
        if ($iframe->tagName !== 'iframe') {
            throw new ProcessorException('Expected YouTube HTML to be iframe but was: ' . $iframe->tagName);
        }

        $iframeSrc = $iframe->getAttribute('src');
        $urlParts = parse_url($iframeSrc);
        if (!$urlParts) {
            throw new ProcessorException('Could not parse URL of YoutTube IFrame: ' . $iframeSrc);
        }

        $newUrl = 'https://www.youtube-nocookie.com';
        $newUrl .= ($urlParts['path'] ?? '');

        $query = ($urlParts['query'] ?? '');
        if ($query) {
            $newUrl .= '?' . $query;
        }

        $iframe->setAttribute('src', $newUrl);

        $response->setHtml($iframe->ownerDocument->saveHTML($iframe));
    }
}
