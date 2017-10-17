<?php

$bootMediaoembed = function () {
    if (!defined('TYPO3_MODE')) {
        die('Access denied.');
    }

    $_EXTKEY = 'mediaoembed';
    $lllPrefix = 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:';

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Sto.' . $_EXTKEY,
        'OembedMediaRenderer',
        ['Oembed' => 'renderMedia'],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['tx_mediaoembed_createrequiredcolumns'] =
        \Sto\Mediaoembed\Install\CreateRequiredColumnsUpdate::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['tx_mediaoembed_migratecontentelements'] =
        \Sto\Mediaoembed\Install\MigrateContentElementsUpdate::class;

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '
mod.wizards.newContentElement {
	wizardItems {
		special.elements {
			mediaoembed_oembedmediarenderer {
				icon = gfx/c_wiz/multimedia.gif
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
};

$bootMediaoembed();
unset($bootMediaoembed);
