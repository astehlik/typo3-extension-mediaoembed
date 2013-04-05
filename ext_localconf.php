<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_content.php']['getData'][] =
	'Sto\\Mediaoembed\\Hooks\\TslibContentGetDataRegisterArray';

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Sto.' . $_EXTKEY,
	'OembedMediaRenderer',
	array('Oembed' => 'renderMedia'),
	array(),
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Sto\\Mediaoembed\\Tasks\\ImportFromEmbedlyTask'] = array(
	'extension'        => $_EXTKEY,
	'title'            => 'Import providers from embed.ly',
	'description'      => 'Creates / updates the name, description and url schemes of the oEmbed providers from embed.ly.',
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Sto\\Mediaoembed\\Tasks\\ImportFromOhhembedTask'] = array(
	'extension'        => $_EXTKEY,
	'title'            => 'Import providers from oohembed.com',
	'description'      => 'Creates non-existing providers with the name, the url scheme and the endpoint from embed.ly.',
);

?>