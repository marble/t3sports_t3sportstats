<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009-2014 Rene Nitzsche (rene[@]system25.de)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

tx_rnbase::load('tx_rnbase_util_Wizicon');

/**
 * Class that adds the wizard icon.
 *
 * @author	René Nitzsche <rene[@]system25.de>
 */
class tx_t3sportstats_util_Wizicon extends tx_rnbase_util_Wizicon {
	/**
	 *
	 */
	protected function getPluginData() {
		$plugins = array();
		$plugins['tx_t3sportstats'] = array(
				'icon'=>t3lib_extMgm::extRelPath('t3sportstats').'/ext_icon.gif',
				'title'=>'plugin.t3sportstats.label',
				'description'=>'plugin.t3sportstats.description',
		);
		return $plugins;
	}
	protected function getLLFile() {
		return t3lib_extMgm::extPath('t3sportstats').'locallang_db.xml';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3sportstats/util/class.tx_t3sportstats_util_Wizicon.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3sportstats/util/class.tx_t3sportstats_util_Wizicon.php']);
}
