<?php

namespace Sto\Mediaoembed\Response\Processor\Support;

use Closure;
use DOMDocument;
use DOMElement;
use Sto\Mediaoembed\Exception\ProcessorException;
use Sto\Mediaoembed\Response\VideoResponse;

trait IframeAwareProcessorTrait
{
    private function modifyIframeUrl(VideoResponse $response, Closure $urlModifier)
    {
        $htmlWrapping = '<html><body><div id="oembed-response">%s</div></body></html>';
        $document = new DOMDocument();
        $loadSuccess = $document->loadHTML(sprintf($htmlWrapping, $response->getHtml()));
        if (!$loadSuccess) {
            throw new ProcessorException('Error parsing HTML from YouTube response.');
        }

        /** @var DOMElement $iframe */
        $iframe = $document->getElementById('oembed-response')->childNodes->item(0);
        if ($iframe->tagName !== 'iframe') {
            throw new ProcessorException('Expected HTML to be iframe but was: ' . $iframe->tagName);
        }

        $iframeSrc = $iframe->getAttribute('src');
        $urlParts = parse_url($iframeSrc);
        if (!$urlParts) {
            throw new ProcessorException('Could not parse URL of IFrame: ' . $iframeSrc);
        }

        $newUrl = $urlModifier($urlParts);

        $iframe->setAttribute('src', $newUrl);

        $modifiedHtml = $iframe->ownerDocument->saveHTML($iframe);
        $response->setHtml($modifiedHtml);
    }
}
