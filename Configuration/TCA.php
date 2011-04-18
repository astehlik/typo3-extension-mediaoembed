<?php
$TCA['tx_mediaoembed_provider'] = array(
	'ctrl' => $TCA['tx_mediaoembed_provider']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'name,hidden,description,regular_expressions,parent_provider'
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
		'regular_expressions' => array(
			'label' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xml:tx_mediaoembed_provider.regular_expressions',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'tx_mediaoembed_provider_regex',
				'foreign_field' => 'provider',
				'foreign_sortby' => 'sorting',
				'appearance' => array(
					'collapseAll' => '1',
					'expandSingle' => '1',
				),
			)
		),
		'parent_provider' => array(
			'label' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xml:tx_mediaoembed_provider.parent_provider',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'tx_mediaoembed_provider',
				'foreign_table_where' => 'AND tx_mediaoembed_provider.uid != ###THIS_UID###',
				'items' => array(
					array('LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xml:tx_mediaoembed_provider.parent_provider.I.0', 0),
				),
				'suppress_icons' => '1',
				'size' => '1',
				'minitems' => '0',
				'maxitems' => '1',
				'wizards' => array(
					'suggest' => array(
						'type' => 'suggest',
					),
				),
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;;;1-1-1, name;;;;2-2-2, description, regular_expressions, parent_provider'),
	),
);

$TCA['tx_mediaoembed_provider_regex'] = array(
	'ctrl' => $TCA['tx_mediaoembed_provider_regex']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'regex,hidden'
	),
	'columns' => array(
		'regex' => array(
			'label' => 'LLL:EXT:mediaoembed/Resources/Private/Language/locallang_db.xml:tx_mediaoembed_provider_regex.regex',
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
		'0' => array('showitem' => 'hidden;;;;1-1-1, regex;;;;2-2-2'),
	),
);
?>