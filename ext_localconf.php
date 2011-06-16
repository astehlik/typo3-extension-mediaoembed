<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/hooks/class.tx_cms_mediaitems.php']['customMediaRenderTypes'][] =
	'tx_mediaoembed_hooks_cmsmediaitems';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/hooks/class.tx_cms_mediaitems.php']['customMediaRender'][] =
	'tx_mediaoembed_hooks_cmsmediaitems';

$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_content.php']['getData'][] =
	'tx_mediaoembed_hooks_tslibcontentgetdataregisterarray';

$GLOBALS['TYPO3_CONF_VARS']['FE']['XCLASS']['tslib/content/class.tslib_content_media.php'] =
	t3lib_extMgm::extPath($_EXTKEY, 'Classes/Ux/TslibContentMedia.php');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_Mediaoembed_Tasks_ImportFromEmbedlyTask'] = array(
	'extension'        => $_EXTKEY,
	'title'            => 'Import providers from embed.ly',
	'description'      => 'Creates / updates the name, description and url schemes of the oEmbed providers from embed.ly.',
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_Mediaoembed_Tasks_ImportFromOhhembedTask'] = array(
	'extension'        => $_EXTKEY,
	'title'            => 'Import providers from oohembed.com',
	'description'      => 'Creates non-existing providers with the name, the url scheme and the endpoint from embed.ly.',
);

?>