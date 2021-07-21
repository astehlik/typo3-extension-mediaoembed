<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Response\Processor\Support;

use Closure;
use DOMDocument;
use DOMElement;
use Sto\Mediaoembed\Exception\ProcessorException;
use Sto\Mediaoembed\Response\HtmlAwareResponseInterface;

trait IframeAwareProcessorTrait
{
    private function addIframeAttributeIfNonExisting(
        HtmlAwareResponseInterface $response,
        string $attribute,
        string $value
    ) {
        $attributeModifier = function (?string $currentValue) use ($value) {
            if ($currentValue) {
                return $currentValue;
            }
            return $value;
        };
        $this->modifyIframeAttribute($response, $attribute, $attributeModifier);
    }

    private function getAttributeValue(DOMElement $iframe, string $attribute): ?string
    {
        $hasAttribute = $iframe->hasAttribute($attribute);
        $attributeValue = null;
        if ($hasAttribute) {
            $attributeValue = $iframe->getAttribute($attribute);
        }
        return $attributeValue;
    }

    private function modifyAttribute(DOMElement $iframe, string $attribute, ?string $attributeValue)
    {
        if ($attributeValue === null) {
            $iframe->removeAttribute($attribute);
            return;
        }

        $iframe->setAttribute($attribute, $attributeValue);
    }

    private function modifyIframeAttribute(
        HtmlAwareResponseInterface $response,
        string $attribute,
        Closure $attributeModifier
    ) {
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

        $attributeValue = $this->getAttributeValue($iframe, $attribute);
        $modifiedAttributeValue = $attributeModifier($attributeValue);

        if ($modifiedAttributeValue === $attributeValue) {
            return;
        }

        $this->modifyAttribute($iframe, $attribute, $modifiedAttributeValue);
        $modifiedHtml = $iframe->ownerDocument->saveHTML($iframe);
        $response->setHtml($modifiedHtml);
    }

    private function modifyIframeUrl(HtmlAwareResponseInterface $response, Closure $urlModifier)
    {
        $attributeModifier = function (?string $iframeSrc) use ($urlModifier) {
            return $urlModifier($iframeSrc);
        };
        $this->modifyIframeAttribute($response, 'src', $attributeModifier);
    }

    private function withoutXmlErrors(Closure $callback)
    {
        $previousSetting = libxml_use_internal_errors(true);
        $callback();
        libxml_use_internal_errors($previousSetting);
    }
}
