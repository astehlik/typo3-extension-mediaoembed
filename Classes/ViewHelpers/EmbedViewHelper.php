<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\ViewHelpers;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Service\ViewFactory;
use Sto\Mediaoembed\ViewHelpers\Behavior\EmbedResponsiveTrait;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;

/**
 * ViewHelper for rendering embedded media content with optional consent.
 * Replaces EmbedResponsivePaddingViewHelper with consent support.
 */
class EmbedViewHelper extends AbstractTagBasedViewHelper
{
    use EmbedResponsiveTrait;

    protected $escapeOutput = false;

    private ViewFactory $viewFactory;

    public function __construct(ViewFactory $viewFactory)
    {
        parent::__construct();
        $this->viewFactory = $viewFactory;
    }

    /**
     * @codeCoverageIgnore
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument('configuration', Configuration::class, '', true);
        $this->registerArgument('response', GenericResponse::class, '', true);
    }

    public function render(): string
    {
        if ($this->getArgumentConfiguration()->isConsentEnabled()) {
            return $this->renderConsentPlaceholder();
        }

        return $this->renderEmbedWithPadding();
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

    private function renderConsentPlaceholder(): string
    {
        $response = $this->getArgumentResponse();
        $html = $this->renderChildren();
        $encodedHtml = base64_encode($html);

        $this->setupEmbedContainer();
        $this->tag->addAttribute('data-oembed-html', $encodedHtml);
        $this->tag->addAttribute('data-provider', $response->getProviderName());
        $this->tag->setContent($this->renderPlaceholderContent($html));

        return $this->tag->render();
    }

    private function renderEmbedWithPadding(): string
    {
        $this->setupEmbedContainer();
        $this->tag->setContent($this->renderChildren());

        return $this->tag->render();
    }

    private function renderPlaceholderContent(string $html): string
    {
        $view = $this->viewFactory->createChildView($this->renderingContext);

        $view->assignMultiple([
            'response' => $this->getArgumentResponse(),
            'configuration' => $this->getArgumentConfiguration(),
            'previewHtml' => htmlspecialchars($html),
        ]);

        return $view->render('Consent/Placeholder');
    }
}
