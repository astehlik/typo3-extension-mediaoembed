<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Functional\Backend;

use Sto\Mediaoembed\Backend\EditDocumentControllerHooks;
use Sto\Mediaoembed\Tests\Functional\AbstractFunctionalTest;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class EditDocumentControllerHooksTest extends AbstractFunctionalTest
{
    public function testAddJsLanguageLabels()
    {
        $pageRendererMock = $this->createMock(PageRenderer::class);

        /** @noinspection PhpParamsInspection */
        $pageRendererMock->expects($this->once())
            ->method('addInlineLanguageLabelArray')
            ->with(
                $this->callback(
                    function (array $translations) {
                        $this->assertArrayContainsExpectedTranslationKeys($translations);
                        $this->assertTranslationsAreNotEmpty($translations);
                        return true;
                    }
                )
            );

        $hooks = new EditDocumentControllerHooks();
        $hooks->setLanguageService($this->getLanguageService());
        $hooks->setPageRenderer($pageRendererMock);
        $hooks->addJsLanguageLabels();
    }

    private function assertArrayContainsExpectedTranslationKeys(array $translations)
    {
        $expectedKeys = array_map(
            function ($value) {
                return 'tx_mediaoembed_' . $value;
            },
            EditDocumentControllerHooks::JS_LABEL_KEYS
        );
        $this->assertEquals($expectedKeys, array_keys($translations));
    }

    private function assertTranslationsAreNotEmpty(array $translations)
    {
        foreach ($translations as $key => $translation) {
            $this->assertNotEmpty($translation, 'Expected translation to be not empty: ' . $key);
        }
    }

    /**
     * @noinspection PhpFullyQualifiedNameUsageInspection PhpUndefinedClassInspection PhpUndefinedNamespaceInspection
     * @return \TYPO3\CMS\Lang\LanguageService|\TYPO3\CMS\Core\Localization\LanguageService
     */
    private function getLanguageService()
    {
        if (!class_exists('TYPO3\CMS\Core\Localization\LanguageService')) {
            return GeneralUtility::makeInstance('TYPO3\CMS\Lang\LanguageService');
        }

        return GeneralUtility::makeInstance('TYPO3\CMS\Core\Localization\LanguageService');
    }
}
