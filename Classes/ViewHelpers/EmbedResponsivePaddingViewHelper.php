<?php

namespace Sto\Mediaoembed\ViewHelpers;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\Contract\AspectRatioAwareResponseInterface;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class EmbedResponsivePaddingViewHelper extends AbstractTagBasedViewHelper
{
    protected $escapeOutput = false;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        parent::__construct();
        $this->configuration = $configuration;
    }

    public function initializeArguments()
    {
        $this->registerArgument('response', AspectRatioAwareResponseInterface::class, '', true);
        $this->registerArgument('style-property', 'string', '', false, 'padding-top');

        $this->registerTagAttribute(
            'class',
            'string',
            'CSS class(es) for this element',
            false,
            'tx-mediaoembed-embed-responsive-padding'
        );
    }

    public function render(): string
    {
        $aspectRatio = $this->getAspectRatio();
        $paddingTop = 100 / $aspectRatio . '%';
        $this->tag->addAttribute('style', $this->arguments['style-property'] . ': ' . $paddingTop . ';');
        $this->tag->setContent($this->renderChildren());
        return $this->tag->render();
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
