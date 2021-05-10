<?php

namespace Sto\Mediaoembed\ViewHelpers;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\Contract\AspectRatioAwareResponseInterface;

class EmbedResponsivePaddingViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function initializeArguments()
    {
        $this->registerArgument('response', AspectRatioAwareResponseInterface::class, '', true);
    }

    public function render(): string
    {
        $aspectRatio = $this->getAspectRatio();
        $paddingTop = 100 / $aspectRatio . '%';
        return '<div class="tx-mediaoembed-embed-responsive-padding" style="padding-top: ' . $paddingTop . '"></div>';
    }

    private function getAspectRatio(): float
    {
        $response = $this->getResponse();
        return $this->configuration->getAspectRatio($response->getAspectRatio());
    }

    private function getResponse(): AspectRatioAwareResponseInterface
    {
        return $this->arguments['response'];
    }
}
