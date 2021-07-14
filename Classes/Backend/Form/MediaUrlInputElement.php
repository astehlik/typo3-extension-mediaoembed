<?php

declare(strict_types=1);

namespace Sto\Mediaoembed\Backend\Form;

use TYPO3\CMS\Backend\Form\Element\InputTextElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

final class MediaUrlInputElement extends InputTextElement
{
    public function render(): array
    {
        $result = parent::render();

        $wrapperId = StringUtility::getUniqueId('tx-mediaoembed-url-input-wrapper-');

        $result['html'] = '<div id="' . $wrapperId . '">' . $result['html'] . '</div>';
        $result['requireJsModules'][] = 'TYPO3/CMS/Mediaoembed/Backend/UrlParser';

        $initJs = 'function(UrlParser) { new UrlParser(' . GeneralUtility::quoteJSvalue($wrapperId) . '); }';

        $result['requireJsModules'][] = ['TYPO3/CMS/Mediaoembed/Backend/UrlParser' => $initJs];

        return $result;
    }
}
