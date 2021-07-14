<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Backend;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;

final class EditDocumentControllerHooks
{
    private $jsLabelKeys = [
        'error_iframe_extraction_failed',
        'error_iframe_has_no_src',
        'error_more_than_one_iframe_found',
        'success_iframe_aspect_ratio_extracted',
        'success_iframe_src_extracted',
    ];

    public function addUrlParserJs()
    {
        $pageRenderer = $this->getPageRenderer();

        $languageLabels = $this->buildLanguageLabelArray();
        $pageRenderer->addInlineLanguageLabelArray($languageLabels);
    }

    private function buildLanguageLabelArray(): array
    {
        $translations = [];

        foreach ($this->jsLabelKeys as $key) {
            $translations['tx_mediaoembed_' . $key] = $this->translate($key);
        }

        return $translations;
    }

    private function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    private function getPageRenderer()
    {
        return GeneralUtility::makeInstance(PageRenderer::class);
    }

    private function translate(string $key): string
    {
        return $this->getLanguageService()->sL(
            'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_be_js.xlf:' . $key
        );
    }
}
