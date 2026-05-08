<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Response\Processor\YouTube;

use InvalidArgumentException;
use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Response\HtmlAwareResponseInterface;
use Sto\Mediaoembed\Response\Processor\HtmlResponseProcessorInterface;
use Sto\Mediaoembed\Response\Processor\Support\IframeManipulator;
use Sto\Mediaoembed\Service\LocalizationService;

readonly class IframeAccessibilityProcessor implements HtmlResponseProcessorInterface
{
    public function __construct(
        private IframeManipulator $iframeManipulator,
        private LocalizationService $localizationService,
    ) {}

    public function processHtmlResponse(HtmlAwareResponseInterface $response): void
    {
        if (!str_starts_with($response->getHtml(), '<iframe')) {
            return;
        }

        if (!$response instanceof GenericResponse) {
            throw new InvalidArgumentException('This processor only works GenericResponse instances!');
        }

        $ariaLabelCallback = fn(): string => $this->getAriaLabel($response);
        $this->iframeManipulator->addIframeAttributeIfNonExisting($response, 'aria-label', $ariaLabelCallback);
    }

    private function getAriaLabel(GenericResponse $response): string
    {
        if (!$response->getTitle()) {
            return $this->localizationService->translate(
                'iframe_aria_label_fallback',
                [$response->getProviderName()],
            );
        }

        return $this->localizationService->translate(
            'iframe_aria_label',
            [
                $response->getProviderName(),
                $response->getTitle(),
            ],
        );
    }
}
