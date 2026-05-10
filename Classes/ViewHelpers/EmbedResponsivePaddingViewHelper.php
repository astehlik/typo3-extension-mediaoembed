<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\ViewHelpers;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\ViewHelpers\Behavior\EmbedResponsiveTrait;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

class EmbedResponsivePaddingViewHelper extends AbstractTagBasedViewHelper
{
    use EmbedResponsiveTrait;

    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument('configuration', Configuration::class, '', true);
        $this->registerArgument('response', GenericResponse::class, '', true);
    }

    public function render(): string
    {
        $this->setupEmbedContainer();

        $this->tag->setContent($this->renderChildren());

        return $this->tag->render();
    }

    protected function getArgumentConfiguration(): Configuration
    {
        return $this->arguments['configuration'];
    }

    protected function getArgumentResponse(): GenericResponse
    {
        return $this->arguments['response'];
    }

    protected function getTagBuilder(): TagBuilder
    {
        return $this->tag;
    }
}
