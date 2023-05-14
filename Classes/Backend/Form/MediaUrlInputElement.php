<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Backend\Form;

use Sto\Mediaoembed\Service\UtilityService;
use TYPO3\CMS\Backend\Form\Element\InputTextElement;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class MediaUrlInputElement extends InputTextElement
{
    private ?UtilityService $utilities = null;

    /**
     * @noinspection PhpMissingParentConstructorInspection Intentionally not calling the deprecated parent constructor!
     */
    public function __construct()
    {
    }

    public function addUrlParserJsToResult(array $result): array
    {
        $this->injectDependencies();

        /** @extensionScannerIgnoreLine  */
        $wrapperId = $this->utilities->getUniqueId('tx-mediaoembed-url-input-wrapper-');

        $result['html'] = '<div id="' . $wrapperId . '">' . $result['html'] . '</div>';

        $result['javaScriptModules'][] = JavaScriptModuleInstruction::create(
            '@de-swebhosting/mediaoembed/backend/url-parser.js'
        )->instance($wrapperId);

        return $result;
    }

    public function injectUtilityService(UtilityService $utilityService): void
    {
        $this->utilities = $utilityService;
    }

    public function render(): array
    {
        $result = parent::render();
        return $this->addUrlParserJsToResult($result);
    }

    private function injectDependencies(): void
    {
        if ($this->utilities) {
            return;
        }
        $this->injectUtilityService(GeneralUtility::makeInstance(UtilityService::class));
    }
}
