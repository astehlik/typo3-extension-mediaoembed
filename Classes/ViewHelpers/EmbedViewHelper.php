<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\ViewHelpers;

use RuntimeException;
use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Response\Contract\AspectRatioAwareResponseInterface;
use Sto\Mediaoembed\Response\GenericResponse;
use Sto\Mediaoembed\Service\ViewFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * ViewHelper for rendering embedded media content with optional consent.
 * Replaces EmbedResponsivePaddingViewHelper with consent support.
 */
class EmbedViewHelper extends AbstractTagBasedViewHelper
{
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
        $this->registerArgument('configuration', Configuration::class, '', true);
        $this->registerArgument('response', GenericResponse::class, '', true);
        $this->registerArgument('style-property', 'string', '', false, 'padding-top');
    }

    public function render(): string
    {
        if ($this->getArgumentConfiguration()->isConsentEnabled()) {
            return $this->renderConsentPlaceholder();
        }

        return $this->renderEmbedWithPadding();
    }

    private function getArgumentConfiguration(): Configuration
    {
        return $this->arguments['configuration'];
    }

    private function getArgumentResponse(): GenericResponse
    {
        return $this->arguments['response'];
    }

    private function getAspectRatio(): float
    {
        $response = $this->getArgumentResponse();

        if (!$response instanceof AspectRatioAwareResponseInterface) {
            throw new RuntimeException('Response must implement AspectRatioAwareResponseInterface');
        }

        return $this->getArgumentConfiguration()->getAspectRatio($response->getAspectRatio());
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

    private function setupEmbedContainer(): void
    {
        $aspectRatio = $this->getAspectRatio();
        $paddingTop = 100 / $aspectRatio . '%';

        $classes = GeneralUtility::trimExplode(' ', $this->tag->getAttribute('class') ?? '', true);
        $classes[] = $this->getArgumentConfiguration()->getEmbedResponsiveClass();

        $this->tag->setTagName('div');
        $this->tag->addAttribute('class', implode(' ', $classes));
        $this->tag->addAttribute('style', $this->arguments['style-property'] . ': ' . $paddingTop . ';');
    }
}
