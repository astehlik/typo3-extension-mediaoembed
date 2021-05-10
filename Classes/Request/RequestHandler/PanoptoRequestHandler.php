<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Request\RequestHandler;

use Sto\Mediaoembed\Content\Configuration;
use Sto\Mediaoembed\Domain\Model\Provider;
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

    public function __construct(Configuration $configuration, ConfigurationManagerInterface $configurationManager)
    {
        $this->configuration = $configuration;
        $this->contentObjectRenderer = $configurationManager->getContentObject();
    }

    public function handle(Provider $provider): array
    {
        $view = new StandaloneView($this->contentObjectRenderer);
        $view->setTemplatePathAndFilename($this->getTemplatePath());
        $view->assign('mediaUrl', $this->configuration->getMediaUrl());

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
