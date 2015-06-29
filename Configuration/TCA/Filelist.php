<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TCA']['tx_jhkestatsexport_domain_model_filelist'] = array(
	'ctrl' => $GLOBALS['TCA']['tx_jhkestatsexport_domain_model_filelist']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, filename, mailsendto, content',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, filename, mailsendto, content, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(

		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_jhkestatsexport_domain_model_filelist',
				'foreign_table_where' => 'AND tx_jhkestatsexport_domain_model_filelist.pid=###CURRENT_PID### AND tx_jhkestatsexport_domain_model_filelist.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),

		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),

		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),

		'filename' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:jh_kestats_export/Resources/Private/Language/locallang_db.xlf:tx_jhkestatsexport_domain_model_filelist.filename',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'mailsendto' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:jh_kestats_export/Resources/Private/Language/locallang_db.xlf:tx_jhkestatsexport_domain_model_filelist.mailsendto',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'eval' => 'trim'
			),
		),
		'content' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:jh_kestats_export/Resources/Private/Language/locallang_db.xlf:tx_jhkestatsexport_domain_model_filelist.content',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'eval' => 'trim'
			)
		),

	),
);
## EXTENSION BUILDER DEFAULTS END TOKEN - Everything BEFORE this line is overwritten with the defaults of the extension builder

// Hide table in web_list
$GLOBALS['TCA']['tx_jhkestatsexport_domain_model_filelist']['ctrl']['hideTable'] = 1;

// Add crdate and tstamp to TCA
$GLOBALS['TCA']['tx_jhkestatsexport_domain_model_filelist']['columns']['crdate'] = Array (
	'exclude' => 1,
	'label' => 'Creation date',
	'config' => Array (
		'type' => 'none',
		'format' => 'datetime',
		'eval' => 'datetime',
	),
);
$GLOBALS['TCA']['tx_jhkestatsexport_domain_model_filelist']['columns']['tstamp'] = Array (
	'exclude' => 1,
	'label' => 'Last update date',
	'config' => Array (
		'type' => 'none',
		'format' => 'datetime',
		'eval' => 'datetime',
	)
);