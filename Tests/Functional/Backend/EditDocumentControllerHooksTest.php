<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Tests\Functional\Backend;

use Psr\Http\Message\ServerRequestInterface;
use Sto\Mediaoembed\Backend\EditDocumentControllerHooks;
use Sto\Mediaoembed\Tests\Functional\AbstractFunctionalTestCase;
use TYPO3\CMS\Backend\Controller\EditDocumentController;
use TYPO3\CMS\Backend\Controller\Event\AfterFormEnginePageInitializedEvent;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class EditDocumentControllerHooksTest extends AbstractFunctionalTestCase
{
    public function testAddJsLanguageLabels(): void
    {
        $pageRendererMock = $this->createMock(PageRenderer::class);

        $pageRendererMock->expects(self::once())
            ->method('addInlineLanguageLabelArray')
            ->with(
                self::callback(
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
        $hooks->__invoke(
            new AfterFormEnginePageInitializedEvent(
                $this->createMock(EditDocumentController::class),
                $this->createMock(ServerRequestInterface::class)
            )
        );
    }

    private function assertArrayContainsExpectedTranslationKeys(array $translations): void
    {
        $expectedKeys = array_map(
            function ($value) {
                return 'tx_mediaoembed_' . $value;
            },
            EditDocumentControllerHooks::JS_LABEL_KEYS
        );
        self::assertSame($expectedKeys, array_keys($translations));
    }

    private function assertTranslationsAreNotEmpty(array $translations): void
    {
        foreach ($translations as $key => $translation) {
            self::assertNotEmpty($translation, 'Expected translation to be not empty: ' . $key);
        }
    }

    private function getLanguageService(): LanguageService
    {
        return GeneralUtility::makeInstance(LanguageServiceFactory::class)->create('default');
    }
}
