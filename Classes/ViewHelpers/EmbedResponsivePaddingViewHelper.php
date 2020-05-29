<?php

namespace Sto\Mediaoembed\ViewHelpers;

use Closure;
use Sto\Mediaoembed\Response\Contract\AspectRatioAwareResponseInterface;

class EmbedResponsivePaddingViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('response', AspectRatioAwareResponseInterface::class, '', true);
    }

    private static function getResponse(array $arguments): AspectRatioAwareResponseInterface
    {
        return $arguments['response'];
    }

    public function render(): string
    {
        $response = self::getResponse($this->arguments);
        $paddingTop = 100 / $response->getAspectRatio() . '%';
        return '<div class="tx-mediaoembed-embed-responsive-padding" style="padding-top: ' . $paddingTop . '"></div>';
    }
}
