<?php
$TCA['tx_mediaoembed_provider'] = array(
	'ctrl' => $TCA['tx_mediaoembed_provider']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'name,hidden,description,url_schemes,endpoint'
	),
	'columns' => array(
		'name' => array(
			'label' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xml:tx_mediaoembed_provider.name',
			'config' => array(
				'type' => 'input',
				'size' => '30',
				'max' => '50',
				'eval' => 'unique,required'
			)
		),
		'hidden' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.disable',
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
				'type' => 'inline',
				'foreign_table' => 'tx_mediaoembed_url_scheme',
				'foreign_field' => 'provider',
				'foreign_sortby' => 'sorting',
				'appearance' => array(
					'collapseAll' => '1',
					'expandSingle' => '1',
				),
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
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;;;1-1-1, name;;;;2-2-2, description, url_schemes, endpoint'),
	),
);

$TCA['tx_mediaoembed_url_scheme'] = array(
	'ctrl' => $TCA['tx_mediaoembed_url_scheme']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'url_scheme,hidden'
	),
	'columns' => array(
		'url_scheme' => array(
			'label' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xml:tx_mediaoembed_url_scheme.url_scheme',
			'config' => array(
				'type' => 'input',
				'size' => '50',
				'max' => '255',
				'eval' => 'required'
			)
		),
		'hidden' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.disable',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'provider' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;;;1-1-1, url_scheme;;;;2-2-2'),
	),
);
?>