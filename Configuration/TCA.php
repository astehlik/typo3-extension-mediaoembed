<?php
$TCA['tx_mediaoembed_provider'] = array(
	'ctrl' => $TCA['tx_mediaoembed_provider']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'name,hidden,is_generic,description,url_schemes,endpoint,use_generic_providers,embedly_shortname'
	),
	'columns' => array(
		'name' => array(
			'label' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xml:tx_mediaoembed_provider.name',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max' => '50',
			)
		),
		'hidden' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.disable',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'is_generic' => array(
			'label' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xml:tx_mediaoembed_provider.is_generic',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'description' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.description',
			'config' => array(
				'type' => 'text',
				'rows' => 5,
				'cols' => 30
			)
		),
		'url_schemes' => array(
			'label' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xml:tx_mediaoembed_provider.url_schemes',
			'config' => array(
				'type' => 'text',
				'rows' => 5,
				'cols' => 30
			)
		),
		'endpoint' => array(
			'label' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xml:tx_mediaoembed_provider.endpoint',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max' => '255',
				'eval' => 'trim'
			)
		),
		'use_generic_providers' => array(
			'label' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xml:tx_mediaoembed_provider.use_generic_providers',
			'config' => array(
				'type' => 'select',
				'size' => '5',
				'maxitems' => '100',
				'foreign_table' => 'tx_mediaoembed_provider',
				'foreign_table_where' => 'AND tx_mediaoembed_provider.is_generic=1',
			)
		),
		'embedly_shortname' => array(
			'label' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xml:tx_mediaoembed_provider.embedly_shortname',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max' => '50',
				'eval' => 'unique'
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;;;1-1-1, name;;;;2-2-2, is_generic, description, url_schemes, endpoint, use_generic_providers, embedly_shortname'),
	),
);
?>