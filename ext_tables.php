<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Table "tx_mediaoembed_provider":
// Information about available oembed providers.
$TCA['tx_mediaoembed_provider'] = array(
	'ctrl' => array(
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete' => 'deleted',
		'sortby' => 'sorting',
		'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
		'adminOnly' => 1,
		'rootLevel' => 1,
		'enablecolumns' => array(
			'disabled' => 'hidden'
		),
		'title' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xml:tx_mediaoembed_provider',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY, 'Configuration/TCA.php'),
		'iconfile'          => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/table_provider.png',
	)
);

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

\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('tt_content');

$GLOBALS['TCA']['tt_content']['palettes']['tx_mediaoembed_settings'] = array(
	'showitem' => 'tx_mediaoembed_url, --linebreak--, tx_mediaoembed_maxwidth, tx_mediaoembed_maxheight',
	'canNotCollapse' => 1,
);

$GLOBALS['TCA']['tt_content']['types']['mediaoembed_oembedmediarenderer']['showitem'] = '
		--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.general;general,
		--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.header;header,
		--palette--;LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xlf:palette.tx_mediaoembed_settings;tx_mediaoembed_settings,
	--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.appearance,
		--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.frames;frames,
	--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
		--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.visibility;visibility,
		--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;access,
	--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.behaviour,
		bodytext;LLL:EXT:cms/locallang_ttc.xml:bodytext.ALT.media_formlabel;;richtext:rte_transform[flag=rte_enabled|mode=ts_css],
	--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.extended';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Media oEmbed');
?>