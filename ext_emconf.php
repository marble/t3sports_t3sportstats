<?php

########################################################################
# Extension Manager/Repository config file for ext: "t3sportstats"
#
# Auto generated 12-02-2008 16:47
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Statistics for T3sports',
	'description' => 'Statistics extension for T3sports.',
	'category' => 'plugin',
	'author' => 'Rene Nitzsche',
	'author_email' => 'rene@system25.de',
	'author_company' => 'System 25',
	'shy' => '',
	'version' => '0.3.1',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.3.0-6.2.99',
			'rn_base' => '0.14.0-0.0.0',
			'cfc_league' => '0.9.1-0.0.0',
			'cfc_league_fe' => '0.9.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:8:{s:9:"ChangeLog";s:4:"690e";s:10:"README.txt";s:4:"ee2d";s:12:"ext_icon.gif";s:4:"1bdc";s:14:"ext_tables.php";s:4:"fc02";s:19:"doc/wizard_form.dat";s:4:"d0ae";s:20:"doc/wizard_form.html";s:4:"1048";s:23:"static/ts/constants.txt";s:4:"96ef";s:19:"static/ts/setup.txt";s:4:"f531";}',
);

?>