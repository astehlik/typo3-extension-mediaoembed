<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\ViewHelpers;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\Contract\AspectRatioAwareResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class EmbedResponsivePaddingViewHelper extends AbstractTagBasedViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('configuration', Configuration::class, '', true);
        $this->registerArgument('response', AspectRatioAwareResponseInterface::class, '', true);
        $this->registerArgument('style-property', 'string', '', false, 'padding-top');
    }

    public function render(): string
    {
        $aspectRatio = $this->getAspectRatio();
        $paddingTop = 100 / $aspectRatio . '%';
        $this->addEmbedReponsiveClass();
        $this->tag->addAttribute('style', $this->arguments['style-property'] . ': ' . $paddingTop . ';');
        $this->tag->setContent($this->renderChildren());
        return $this->tag->render();
    }

    private function addEmbedReponsiveClass(): void
    {
        $classes = GeneralUtility::trimExplode(' ', $this->tag->getAttribute('class') ?? '', true);

        $classes[] = $this->getConfiguration()->getEmbedResponsiveClass();

        $this->tag->addAttribute('class', implode(' ', $classes));
    }

    private function getAspectRatio(): float
    {
        $response = $this->getResponse();
        return $this->getConfiguration()->getAspectRatio($response->getAspectRatio());
    }

    private function getConfiguration(): Configuration
    {
        return $this->arguments['configuration'];
    }

    private function getResponse(): AspectRatioAwareResponseInterface
    {
        return $this->arguments['response'];
    }
}
