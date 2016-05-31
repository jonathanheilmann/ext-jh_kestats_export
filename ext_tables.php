<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (TYPO3_MODE === 'BE') {

	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'Heilmann.' . $_EXTKEY,
		'web',	 // Make module a submodule of 'web'
		'list',	// Submodule key
		'',						// Position
		array(
			'Filelist' => 'list, new, create, delete',

		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_list.xlf',
		)
	);

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_jhkestatsexport_domain_model_filelist', 'EXT:jh_kestats_export/Resources/Private/Language/locallang_csh_tx_jhkestatsexport_domain_model_filelist.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_jhkestatsexport_domain_model_filelist');
$GLOBALS['TCA']['tx_jhkestatsexport_domain_model_filelist'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:jh_kestats_export/Resources/Private/Language/locallang_db.xlf:tx_jhkestatsexport_domain_model_filelist',
		'label' => 'filename',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'versioningWS' => 2,
		'versioning_followPages' => TRUE,

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'filename,mailsendto,content,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Filelist.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_jhkestatsexport_domain_model_filelist.gif',

		'rootLevel' => 1
	),
);