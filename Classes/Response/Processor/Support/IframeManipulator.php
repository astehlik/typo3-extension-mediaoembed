<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Response\Processor\Support;

use Closure;
use DOMDocument;
use DOMElement;
use Sto\Mediaoembed\Exception\InvalidUrlException;
use Sto\Mediaoembed\Exception\ProcessorException;
use Sto\Mediaoembed\Response\HtmlAwareResponseInterface;
use Sto\Mediaoembed\Service\UrlService;
use TYPO3\CMS\Core\Http\Uri;

final readonly class IframeManipulator
{
    public function __construct(
        private UrlService $urlService,
    ) {}

    /**
     * @param callable(): string|string $value
     */
    public function addIframeAttributeIfNonExisting(
        HtmlAwareResponseInterface $response,
        string $attribute,
        string|callable $value,
    ): void {
        $attributeModifier = static fn(?string $currentValue): string => $currentValue !== null && $currentValue !== ''
            ? $currentValue
            : (is_string($value) ? $value : $value());

        $this->modifyIframeAttribute($response, $attribute, $attributeModifier);
    }

    /**
     * @param callable(?Uri $iframeSrc): ?Uri $urlModifier
     */
    public function modifyIframeUrl(HtmlAwareResponseInterface $response, callable $urlModifier): void
    {
        $attributeModifier = function (?string $iframeSrc) use ($urlModifier): ?string {
            $iframeUri = $this->parseUriIfPossible($iframeSrc);

            $uri = $urlModifier($iframeUri);

            if ($uri === null) {
                return null;
            }

            return (string)$uri;
        };

        $this->modifyIframeAttribute($response, 'src', $attributeModifier);
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

    private function modifyAttribute(DOMElement $iframe, string $attribute, ?string $attributeValue): void
    {
        if ($attributeValue === null) {
            $iframe->removeAttribute($attribute);
            return;
        }

        $iframe->setAttribute($attribute, $attributeValue);
    }

    /**
     * @param callable(?string $currentValue): ?string $attributeModifier
     */
    private function modifyIframeAttribute(
        HtmlAwareResponseInterface $response,
        string $attribute,
        callable $attributeModifier,
    ): void {
        $document = new DOMDocument();
        $loadSuccess = false;
        $this->withoutXmlErrors(
            static function () use ($response, $document, &$loadSuccess): void {
                $xmlPrefixForEncodingFix = '<?xml version="1.0" encoding="utf-8" ?>';
                $htmlWrapping = '<html lang="de"><body><div id="oembed-response">%s</div></body></html>';
                $template = $xmlPrefixForEncodingFix . $htmlWrapping;
                $loadSuccess = $document->loadHTML(sprintf($template, $response->getHtml()));
            },
        );
        if (!$loadSuccess) {
            // No possiblity was found to let this fail, therefore coverage is ignored.
            throw new ProcessorException('Error parsing HTML from YouTube response.'); // @codeCoverageIgnore
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

    private function parseUriIfPossible(?string $iframeSrc): ?Uri
    {
        if (!is_string($iframeSrc)) {
            return null;
        }

        try {
            return $this->urlService->parseUrl($iframeSrc);
        } catch (InvalidUrlException) {
            return null;
        }
    }

    private function withoutXmlErrors(Closure $callback): void
    {
        $previousSetting = libxml_use_internal_errors(true);
        $callback();
        libxml_use_internal_errors($previousSetting);
    }
}
