<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Response\Processor\YouTube;

use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Response\HtmlAwareResponseInterface;
use Sto\Mediaoembed\Response\Processor\HtmlResponseProcessorInterface;
use Sto\Mediaoembed\Response\Processor\Support\IframeAwareProcessorTrait;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class IframeAccessibilityProcessor implements HtmlResponseProcessorInterface
{
    use IframeAwareProcessorTrait;

    public function processHtmlResponse(HtmlAwareResponseInterface $response): void
    {
        if (strpos($response->getHtml(), '<iframe') !== 0) {
            return;
        }

        if (!$response instanceof GenericResponse) {
            throw new \InvalidArgumentException('This processor only works GenericResponse instances!');
        }

        $ariaLabel = $this->getAriaLabel($response);
        $this->addIframeAttributeIfNonExisting($response, 'aria-label', $ariaLabel);
    }

    /**
     * @return string|null
     */
    private function getAriaLabel(GenericResponse $response)
    {
        if (!$response->getTitle()) {
            return LocalizationUtility::translate(
                'iframe_aria_label_fallback',
                'Mediaoembed',
                [$response->getProviderName()]
            );
        }

        return LocalizationUtility::translate(
            'iframe_aria_label',
            'Mediaoembed',
            [
                $response->getProviderName(),
                $response->getTitle(),
            ]
        );
    }
}
