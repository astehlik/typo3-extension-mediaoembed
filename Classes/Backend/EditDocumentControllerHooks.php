<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Backend;

use TYPO3\CMS\Backend\Controller\Event\AfterFormEnginePageInitializedEvent;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class EditDocumentControllerHooks
{
    public const JS_LABEL_KEYS = [
        'error_iframe_extraction_failed',
        'error_iframe_has_no_src',
        'error_more_than_one_iframe_found',
        'success_iframe_aspect_ratio_extracted',
        'success_iframe_src_extracted',
    ];

    private ?LanguageService $languageService = null;

    private ?PageRenderer $pageRenderer = null;

    public function __invoke(AfterFormEnginePageInitializedEvent $afterFormEnginePageInitializedEvent): void
    {
        $this->addJsLanguageLabels();
    }

    public function addJsLanguageLabels(): void
    {
        $this->initDependencies();

        $languageLabels = $this->buildLanguageLabelArray();
        $this->pageRenderer->addInlineLanguageLabelArray($languageLabels);
    }

    public function setLanguageService(LanguageService $languageService): void
    {
        $this->languageService = $languageService;
    }

    public function setPageRenderer(PageRenderer $pageRenderer): void
    {
        $this->pageRenderer = $pageRenderer;
    }

    private function buildLanguageLabelArray(): array
    {
        $translations = [];

        foreach (self::JS_LABEL_KEYS as $key) {
            $translations['tx_mediaoembed_' . $key] = $this->translate($key);
        }

        return $translations;
    }

    private function initDependencies(): void
    {
        if ($this->languageService) {
            return;
        }
        $this->setLanguageService($GLOBALS['LANG']);
        $this->setPageRenderer(GeneralUtility::makeInstance(PageRenderer::class));
    }

    private function translate(string $key): string
    {
        return $this->languageService->sL(
            'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_be_js.xlf:' . $key,
        );
    }
}
