<?php

namespace Sto\Mediaoembed\ViewHelpers;

use Closure;
use Sto\Mediaoembed\Response\Contract\AspectRatioAwareResponseInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class EmbedResponsivePaddingViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('response', AspectRatioAwareResponseInterface::class, '', true);
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $response = self::getResponse($arguments);
        $paddingTop = 100 / $response->getAspectRatio() . '%';
        return '<div class="tx-mediaoembed-embed-responsive-padding" style="padding-top: ' . $paddingTop . '"></div>';
    }

    private static function getResponse(array $arguments): AspectRatioAwareResponseInterface
    {
        return $arguments['response'];
    }
}
