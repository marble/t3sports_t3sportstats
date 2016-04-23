<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "t3sportstats".
 *
 * Auto generated 06-01-2016 17:12
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Statistics for T3sports',
	'description' => 'Statistics extension for T3sports.',
	'category' => 'plugin',
	'version' => '0.3.0',
	'state' => 'alpha',
	'uploadfolder' => 0,
	'createDirs' => '',
	'clearcacheonload' => 1,
	'author' => 'Rene Nitzsche',
	'author_email' => 'rene@system25.de',
	'author_company' => 'System 25',
	'constraints' => 
	array (
		'depends' => 
		array (
			'cms' => '',
			'rn_base' => '0.9.4-0.0.0',
			'cfc_league' => '0.8.0-0.0.0',
			'cfc_league_fe' => '0.8.0-0.0.0',
		),
		'conflicts' => 
		array (
		),
		'suggests' => 
		array (
		),
	),
);

