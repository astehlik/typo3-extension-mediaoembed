<?php
if (!defined ('TYPO3_MODE'))	die ('Access denied.');

/**
 * Table "tx_mediaoembed_provider":
 * Information about available oembed providers.
 */
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
		'dynamicConfigFile' => t3lib_extMgm::extPath('mediaoembed', 'Configuration/TCA.php'),
	)
);

/**
 * Table "tx_mediaoembed_url_scheme":
 * Regular expressions for the available providers
 */
$TCA['tx_mediaoembed_url_scheme'] = array(
	'ctrl' => array(
		'label' => 'url_scheme',	
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
		'title' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xml:tx_mediaoembed_url_scheme',
		'dynamicConfigFile' => t3lib_extMgm::extPath('mediaoembed', 'Configuration/TCA.php'),
	)
);

?>