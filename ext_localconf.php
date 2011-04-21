<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/hooks/class.tx_cms_mediaitems.php']['customMediaRenderTypes'][] =
	'tx_mediaoembed_hooks_cmsmediaitems';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/hooks/class.tx_cms_mediaitems.php']['customMediaRender'][] =
	'tx_mediaoembed_hooks_cmsmediaitems';

$GLOBALS['TYPO3_CONF_VARS']['FE']['XCLASS']['tslib/content/class.tslib_content_media.php'] =
	t3lib_extMgm::extPath($_EXTKEY, 'Classes/Ux/class.ux_tslib_content_media.php');
?>