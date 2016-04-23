<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

////////////////////////////////
// Plugin anmelden
////////////////////////////////
// Einige Felder ausblenden
$TCA['tt_content']['types']['list']['subtypes_excludelist']['tx_t3sportstats']='layout,select_key,pages';

// Das tt_content-Feld pi_flexform einblenden
$TCA['tt_content']['types']['list']['subtypes_addlist']['tx_t3sportstats']='pi_flexform';

t3lib_extMgm::addPiFlexFormValue('tx_t3sportstats','FILE:EXT:'.$_EXTKEY.'/flexform_main.xml');
t3lib_extMgm::addPlugin(Array('LLL:EXT:'.$_EXTKEY.'/locallang_db.php:plugin.t3sportstats.label','tx_t3sportstats'));

# Add plugin wizard
if (TYPO3_MODE=='BE')	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_t3sportstats_util_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'util/class.tx_t3sportstats_util_Wizicon.php';

t3lib_extMgm::addStaticFile($_EXTKEY,'static/ts/', 'T3sportstats');
?>