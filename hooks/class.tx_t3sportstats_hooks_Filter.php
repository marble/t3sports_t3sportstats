<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Rene Nitzsche
 *  Contact: rene@system25.de
 *  All rights reserved
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 ***************************************************************/

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');


/**
 * Make additional fields for match filter
 * 
 * @author Rene Nitzsche
 */
class tx_t3sportstats_hooks_Filter {
	private static $tableData = array(
		'player' => array('tableAlias' => 'PLAYERSTAT', 'colName' => 'player'),
		'coach' => array('tableAlias' => 'COACHSTAT', 'colName' => 'coach'),
		'referee' => array('tableAlias' => 'REFEREESTAT', 'colName' => 'referee'),
	);

	public function handleMatchFilter($params, $parent) {
		$configurations = $params['configurations'];
		$parameters = $configurations->getParameters();
		$statsType = $parameters->get('statstype');
		if(!$statsType) return;
		$profileType = 'player';
		$profile = $parameters->getInt($profileType);
		if(!$profile) {
			$profileType = 'coach';
			$profile = $parameters->getInt($profileType);
		}
		if(!$profile) {
			$profileType = 'referee';
			$profile = $parameters->getInt($profileType);
		}


		if(!$profile) return;

		
		$fields =& $params['fields'];
		$confId = $params['confid'];
		$confId .= 'filter.stats.'.$statsType.'.';

		$cols = $configurations->get($confId.'columns');
		if(!$cols) return;
		$cols = array_flip(t3lib_div::trimExplode(',', $cols));

		$statsKey = $parameters->get('statskey');
		if($statsKey && array_key_exists(strtolower($statsKey), $cols)) {
			$fields[self::$tableData[$profileType]['tableAlias'].'.'.strtoupper($statsKey)][OP_GT_INT] = 0;
		}
		else return;

		// Ziel ist ein JOIN auf die playerstats, für den aktuellen Spieler und die aktuellen 
		// fields der stats
		tx_rnbase_util_SearchBase::setConfigFields($fields, $configurations, $confId.'fields.');
		$fields[self::$tableData[$profileType]['tableAlias'].'.'.self::$tableData[$profileType]['colName']][OP_EQ_INT] = $profile;
		$parent->addFilterData($profileType, $profile);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3sportstats/hooks/class.tx_t3sportstats_hooks_Filter.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3sportstats/hooks/class.tx_t3sportstats_hooks_Filter.php']);
}

?>