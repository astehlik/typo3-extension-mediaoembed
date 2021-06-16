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
        $document = new DOMDocument();
        $loadSuccess = false;
        $this->withoutXmlErrors(
            function () use ($response, $document, &$loadSuccess) {
                $htmlWrapping = '<html><body><div id="oembed-response">%s</div></body></html>';
                $loadSuccess = $document->loadHTML(sprintf($htmlWrapping, $response->getHtml()));
            }
        );
        if (!$loadSuccess) {
            throw new ProcessorException('Error parsing HTML from YouTube response.');
        }

        /** @var DOMElement $iframe */
        $iframe = $document->getElementById('oembed-response')->childNodes->item(0);
        if ($iframe->tagName !== 'iframe') {
            throw new ProcessorException('Expected HTML to be iframe but was: ' . $iframe->tagName);
        }

        $iframeSrc = $iframe->getAttribute('src');

        $newUrl = $urlModifier($iframeSrc);

        $iframe->setAttribute('src', $newUrl);

        $modifiedHtml = $iframe->ownerDocument->saveHTML($iframe);
        $response->setHtml($modifiedHtml);
    }

    private function withoutXmlErrors(Closure $callback)
    {
        $previousSetting = libxml_use_internal_errors(true);
        $callback();
        libxml_use_internal_errors($previousSetting);
    }
}
