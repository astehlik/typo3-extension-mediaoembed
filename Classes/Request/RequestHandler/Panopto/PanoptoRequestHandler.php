<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Request\RequestHandler\Panopto;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Domain\Model\Provider;
use Sto\Mediaoembed\Request\RequestHandler\RequestHandlerInterface;
use Sto\Mediaoembed\Request\RequestHandler\SettingsAwareRequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

final class PanoptoRequestHandler implements RequestHandlerInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ContentObjectRenderer|null
     */
    private $contentObjectRenderer;

    /**
     * @var PanoptoUrlProcessor
     */
    private $urlProcessor;

    public function __construct(
        Configuration $configuration,
        ConfigurationManagerInterface $configurationManager,
        PanoptoUrlProcessor $urlProcessor
    ) {
        $this->configuration = $configuration;
        $this->contentObjectRenderer = $configurationManager->getContentObject();
        $this->urlProcessor = $urlProcessor;
    }

    public function handle(Provider $provider): array
    {
        $view = new StandaloneView($this->contentObjectRenderer);
        $view->setTemplatePathAndFilename($this->getTemplatePath());

        $settings = $provider->getRequestHandlerSettings();

        $mediaUrl = $this->configuration->getMediaUrl();
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
            'EXT:mediaoembed/Resources/Private/Templates/RequestHandler/Panopto.html'
        );
    }
}
