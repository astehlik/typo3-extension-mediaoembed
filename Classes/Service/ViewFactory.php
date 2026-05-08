<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Service;

use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

readonly class ViewFactory
{
    public function __construct(
        private ViewFactoryInterface $viewFactory
    ) {}

    public function createChildView(?RenderingContextInterface $renderingContext): ViewInterface
    {
        if ($renderingContext === null) {
            throw new RuntimeException('Rendering context is required');
        }

        $request = $renderingContext->hasAttribute(ServerRequestInterface::class)
            ? $renderingContext->getAttribute(ServerRequestInterface::class)
            : null;

        $templatePaths = $renderingContext->getTemplatePaths();

        $viewFactoryData = new ViewFactoryData(
            templateRootPaths: $templatePaths->getTemplateRootPaths(),
            partialRootPaths: $templatePaths->getPartialRootPaths(),
            layoutRootPaths: $templatePaths->getLayoutRootPaths(),
            request: $request,
        );

        return $this->viewFactory->create($viewFactoryData);
    }
}
