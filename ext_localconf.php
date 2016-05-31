<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// Add file indexing task
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Heilmann\\JhKestatsExport\\Task\\ExportTask'] = array(
	'extension' => $_EXTKEY,
	'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:tx_jhkestatsexport_task.title',
	'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:tx_jhkestatsexport_task.description',
	'additionalFields' => \Heilmann\JhKestatsExport\Task\ExportAdditionalFieldProvider::class
);