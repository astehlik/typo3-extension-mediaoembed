<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\ViewHelpers\Behavior;

use RuntimeException;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\Contract\AspectRatioAwareResponseInterface;
use Sto\Mediaoembed\Response\GenericResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

trait EmbedResponsiveTrait
{
    abstract protected function getArgumentConfiguration(): Configuration;

    abstract protected function getArgumentResponse(): GenericResponse;

    abstract protected function getTagBuilder(): TagBuilder;

    private function appendToAttribute(string $attributeName, string $value): void
    {
        $currentValue = $this->getTagBuilder()->getAttribute($attributeName) ?? '';

        $values = GeneralUtility::trimExplode(' ', $currentValue, true);

        $values[] = $value;

        $this->getTagBuilder()->addAttribute($attributeName, implode(' ', $values));
    }

    private function getAspectRatio(): float
    {
        $response = $this->getArgumentResponse();

        if (!$response instanceof AspectRatioAwareResponseInterface) {
            throw new RuntimeException('Response must implement AspectRatioAwareResponseInterface');
        }

        return $this->getArgumentConfiguration()->getAspectRatio($response->getAspectRatio());
    }

    private function setupEmbedContainer(): void
    {
        $aspectRatio = $this->getAspectRatio();
        $paddingTop = 100 / $aspectRatio . '%';

        $this->getTagBuilder()->setTagName('div');

        $configuration = $this->getArgumentConfiguration();
        $this->appendToAttribute('class', $configuration->getEmbedResponsiveClass());
        $this->appendToAttribute('style', $configuration->getEmbedResponsiveStyleProperty() . ': ' . $paddingTop . ';');
    }
}
