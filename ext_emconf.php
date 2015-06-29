<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "jh_kestats_export".
 *
 * Auto generated 09-12-2014 15:57
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Export ke_stats',
	'description' => 'Export ke_stats data as pdf with some more grafics. Export may be done manually or automatically by cronjob (scheduler).',
	'category' => 'module',
	'version' => '1.0.4',
	'state' => 'beta',
	'uploadfolder' => false,
	'createDirs' => '',
	'clearcacheonload' => true,
	'author' => 'Jonathan Heilmann',
	'author_email' => 'mail@jonathan-heilmann.de',
	'author_company' => '',
	'constraints' => 
	array (
		'depends' => 
		array (
			'typo3' => '6.2.0-6.2.99',
		),
		'conflicts' => 
		array (
		),
		'suggests' => 
		array (
		),
	),
);

