<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Backend\Form;

use Sto\Mediaoembed\Service\UtilityService;
use TYPO3\CMS\Backend\Form\Element\InputTextElement;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;

final class MediaUrlInputElement extends InputTextElement
{
    private ?UtilityService $utilities = null;

    public function addUrlParserJsToResult(array $result): array
    {
        /** @extensionScannerIgnoreLine  */
        $wrapperId = $this->utilities->getUniqueId('tx-mediaoembed-url-input-wrapper-');

        $result['html'] = '<div id="' . $wrapperId . '">' . $result['html'] . '</div>';

        $result['javaScriptModules'][] = JavaScriptModuleInstruction::create(
            '@de-swebhosting/mediaoembed/backend/url-parser.js',
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
}
