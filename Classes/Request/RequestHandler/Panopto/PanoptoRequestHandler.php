<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Request\RequestHandler\Panopto;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Request\RequestHandler\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;

final readonly class PanoptoRequestHandler implements RequestHandlerInterface
{
    public function __construct(
        private PanoptoUrlProcessor $urlProcessor,
        private ViewFactoryInterface $viewFactory,
    ) {}

    public function handle(Provider $provider, Configuration $configuration): array
    {
        $viewFactoryData = new ViewFactoryData(templatePathAndFilename: $this->getTemplatePath());
        $view = $this->viewFactory->create($viewFactoryData);

        $settings = $provider->getRequestHandlerSettings();

        $mediaUrl = $configuration->getMediaUrl();
        $mediaUrl = $this->urlProcessor->processUrl($mediaUrl, $settings['defaultViewerUrlParameters'] ?? []);
        $view->assign('mediaUrl', $mediaUrl);

        return [
            'type' => 'video',
            'html' => $view->render(),
            'provider_name' => 'Panopto',
        ];
    }

    private function getTemplatePath(): string
    {
        return GeneralUtility::getFileAbsFileName(
            'EXT:mediaoembed/Resources/Private/Templates/RequestHandler/Panopto.html',
        );
    }
}
