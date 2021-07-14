<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Backend;

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class EditDocumentControllerHooks
{
    const JS_LABEL_KEYS = [
        'error_iframe_extraction_failed',
        'error_iframe_has_no_src',
        'error_more_than_one_iframe_found',
        'success_iframe_aspect_ratio_extracted',
        'success_iframe_src_extracted',
    ];

    public function addJsLanguageLabels()
    {
        $pageRenderer = $this->getPageRenderer();

        $languageLabels = $this->buildLanguageLabelArray();
        $pageRenderer->addInlineLanguageLabelArray($languageLabels);
    }

    private function buildLanguageLabelArray(): array
    {
        $translations = [];

        foreach (self::JS_LABEL_KEYS as $key) {
            $translations['tx_mediaoembed_' . $key] = $this->translate($key);
        }

        return $translations;
    }

    /**
     * @noinspection PhpFullyQualifiedNameUsageInspection
     * @noinspection PhpUndefinedClassInspection
     * @noinspection PhpUndefinedNamespaceInspection
     *
     * @return \TYPO3\CMS\Lang\LanguageService|\TYPO3\CMS\Core\Localization\LanguageService
     */
    private function getLanguageService()
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
