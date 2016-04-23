<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008-2010 Rene Nitzsche
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
tx_rnbase::load('tx_rnbase_filter_BaseFilter');

/**
 * Extend marker classes
 * 
 * @author Rene Nitzsche
 */
class tx_t3sportstats_hooks_Marker {
	public static $filterData = array(
		'player' => array('tableAlias' => 'PLAYERSTAT', 'colName' => 'player', 'search' => 'searchPlayerStats'),
		'coach' => array('tableAlias' => 'COACHSTAT', 'colName' => 'coach', 'search' => 'searchCoachStats'),
		'referee' => array('tableAlias' => 'REFEREESTAT', 'colName' => 'referee', 'search' => 'searchRefereeStats'),
	);

	/**
	 * Extend profileMarker for statistical data about profile
	 * @param array $params
	 * @param tx_cfcleaguefe_util_ProfileMarker $parent
	 */
	public function parseProfile($params, $parent) {
//t3lib_utility_Debug::debug($params, 'class.tx_t3sportstats_hooks_Marker.php'); // TODO: remove me
		// Wir benötigen mehrere Statistiken pro Person
		// Diese müssen per TS konfiguriert werden
		// stats.liga.fields..
		// Marker: ###PROFILE_STATS_LIGA###
		$config = $params['conf'];
		$confId = $params['confId'].'stats.';
		$profile = $params['item'];
		$template = $params['template'];
		$markerPrefix = $params['marker'];
		
		$subpartArray = array();
		$statKeys = $config->getKeyNames($confId);
		foreach($statKeys As $statKey) {
			// Die Daten holen
			$subpartMarker = $markerPrefix.'_STATS_'.strtoupper($statKey);

			$subpart = tx_rnbase_util_Templates::getSubpart($template, '###'.$subpartMarker.'###');
			if(!$subpart) continue;
			$items = $this->findData($profile, $config, $confId, $statKey);
			// Markerklasse aus Config holen
			$markerClass = $config->get($confId.$statKey.'.markerClass');
			$markerClass = $markerClass ? $markerClass : 'tx_t3sportstats_marker_PlayerStats';
			$marker = tx_rnbase::makeInstance($markerClass);
			// Wir sollten nur einen Datensatz haben und können diesen jetzt ausgeben
			$subpartArray['###'.$subpartMarker.'###'] = $marker->parseTemplate($subpart, $items[0], $config->getFormatter(), $confId.$statKey.'.data.', $subpartMarker);
		}

		$params['template'] = tx_rnbase_util_Templates::substituteMarkerArrayCached($template, array(), $subpartArray);
	}

	private function findData($profile, $configurations, $confId, $type) {
		$srv = tx_t3sportstats_util_ServiceRegistry::getStatisticService();
		$confId = $confId.$type.'.';
		$filter = tx_rnbase_filter_BaseFilter::createFilter(new ArrayObject(), $configurations, new ArrayObject(), $confId);

		$fields = array();
		$filterType = $configurations->get($confId.'filterType');
		if(!$filterType) throw new Exception('t3sportstats: No filter type configured in ' . $confId.'filterType');
		$filterType = strtolower($filterType);
		$fields[self::$filterData[$filterType]['tableAlias'].'.'.self::$filterData[$filterType]['colName']][OP_EQ_INT] = $profile->getUid();
		$options = array('enablefieldsoff' => 1);
//		$options['debug'] = 1;
		$filter->init($fields, $options);

		$searchMethod = self::$filterData[$filterType]['search'];
		$items = $srv->$searchMethod($fields, $options);
		return $items;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3sportstats/hooks/class.tx_t3sportstats_hooks_Marker.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3sportstats/hooks/class.tx_t3sportstats_hooks_Marker.php']);
}

?>