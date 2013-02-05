<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/hooks/class.tx_cms_mediaitems.php']['customMediaRenderTypes'][] =
	'Sto\\Mediaoembed\\Hooks\\CmsMediaitems';

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/hooks/class.tx_cms_mediaitems.php']['customMediaRender'][] =
	'Sto\\Mediaoembed\\Hooks\\CmsMediaitems';

$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_content.php']['getData'][] =
	'Sto\\Mediaoembed\\Hooks\\TslibContentGetDataRegisterArray';

$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects']['TYPO3\\CMS\\Frontend\\MediaWizard\\MediaWizardProvider']['className'] = 'Sto\\Mediaoembed\\Hooks\\DisabledMediaWizardProvider';

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