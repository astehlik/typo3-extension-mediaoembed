<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$ttContentColumns = array(
	'tx_mediaoembed_url' => array(
		'label' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:tt_content.tx_mediaoembed_url',
		'config' => array(
			'type' => 'input',
		)
	),
	'tx_mediaoembed_maxwidth' => array(
		'label' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:tt_content.tx_mediaoembed_maxwidth',
		'config' => array(
			'type' => 'input',
			'size' => '4',
			'max' => '4',
			'eval' => 'int',
			'range' => array(
				'upper' => '999',
				'lower' => '25'
			),
			'default' => 0
		)
	),
	'tx_mediaoembed_maxheight' => array(
		'label' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:tt_content.tx_mediaoembed_maxheight',
		'config' => array(
			'type' => 'input',
			'size' => '4',
			'max' => '4',
			'eval' => 'int',
			'range' => array(
				'upper' => '999',
				'lower' => '25'
			),
			'default' => 0
		)
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_content', $ttContentColumns, TRUE);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem(
	'tt_content',
	'CType',
	array(
		'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:tt_content.CType.I.tx_mediaoembed',
		'mediaoembed_oembedmediarenderer',
		'i/tt_content_mm.gif'
	),
	'media',
	'after'
);

$GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes']['mediaoembed_oembedmediarenderer'] = 'extensions-mediaoembed-content-externalmedia';

$GLOBALS['TCA']['tt_content']['palettes']['tx_mediaoembed_settings'] = array(
	'showitem' => 'tx_mediaoembed_url, --linebreak--, tx_mediaoembed_maxwidth, tx_mediaoembed_maxheight',
	'canNotCollapse' => 1,
);

$GLOBALS['TCA']['tt_content']['types']['mediaoembed_oembedmediarenderer']['showitem'] = '
		--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.general;general,
		--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.header;header,
		--palette--;LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:palette.tx_mediaoembed_settings;tx_mediaoembed_settings,
	--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.appearance,
		--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.frames;frames,
	--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access,
		--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.visibility;visibility,
		--palette--;LLL:EXT:cms/locallang_ttc.xlf:palette.access;access,
	--div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.extended';
