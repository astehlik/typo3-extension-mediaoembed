<?php

/** @noinspection PhpFullyQualifiedNameUsageInspection */

/** @noinspection PhpMissingStrictTypesDeclarationInspection */

defined('TYPO3') or die();

$bootMediaoembed = function () {
    $lllPrefix = 'LLL:' . 'EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:';

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Mediaoembed',
        'OembedMediaRenderer',
        /** @uses \Sto\Mediaoembed\Controller\OembedController::renderMediaAction() */
        [\Sto\Mediaoembed\Controller\OembedController::class => 'renderMedia'],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '
mod.wizards.newContentElement {
	wizardItems {
		special.elements {
			mediaoembed_oembedmediarenderer {
				iconIdentifier = extensions-mediaoembed-content-externalmedia
				title = ' . $lllPrefix . 'tt_content.CType.I.tx_mediaoembed
				description = ' . $lllPrefix . 'new_content_element_wizard_oembedmediarenderer_description
				tt_content_defValues {
					CType = mediaoembed_oembedmediarenderer
				}
			}
		}
		special.show := addToList(mediaoembed_oembedmediarenderer)
	}
}
'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][\Sto\Mediaoembed\Backend\AspectRatioEvaluation::class]
        = '';

    $dispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class);
    /** @uses \Sto\Mediaoembed\Backend\EditDocumentControllerHooks::addJsLanguageLabels() */
    $dispatcher->connect(
        \TYPO3\CMS\Backend\Controller\EditDocumentController::class,
        'initAfter',
        \Sto\Mediaoembed\Backend\EditDocumentControllerHooks::class,
        'addJsLanguageLabels'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['tx_mediaoembed_urlinput'] = [
        'nodeName' => 'tx_mediaoembed_urlinput',
        'class' => \Sto\Mediaoembed\Backend\Form\MediaUrlInputElement::class,
        'priority' => 50,
    ];
};

$bootMediaoembed();
unset($bootMediaoembed);
