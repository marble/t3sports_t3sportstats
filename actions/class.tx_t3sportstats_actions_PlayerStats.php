<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Rene Nitzsche (rene@system25.de)
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

require_once(t3lib_extMgm::extPath('rn_base') . 'class.tx_rnbase.php');

tx_rnbase::load('tx_rnbase_action_BaseIOC');
tx_rnbase::load('tx_rnbase_filter_BaseFilter');


/**
 * Controller für Suchformular für Dokumenten
 * 
 */
class tx_t3sportstats_actions_PlayerStats extends tx_rnbase_action_BaseIOC {

	/**
	 * 
	 *
	 * @param array_object $parameters
	 * @param tx_rnbase_configurations $configurations
	 * @param array $viewData
	 * @return string error msg or null
	 */
	public function handleRequest(&$parameters,&$configurations, &$viewData){
		// Zuerst die Art der Statistik ermitteln
		$types = t3lib_div::trimExplode(',', $configurations->get($this->getConfId().'statisticTypes'), 1);
		if(!count($types)) {
			// Abbruch kein Typ angegeben
			throw new Exception('No statistics type configured in: ' . $this->getConfId().'statisticTypes');
		}
		

		$statsData = array();
		foreach ($types as $type) {
			$statsData[$type] = $this->findData($parameters, $configurations, $viewData, $type);
		}
		$viewData->offsetSet('items', $statsData);
		$teamId = $configurations->get($this->getConfId().'highlightTeam');
		if($teamId) {
			tx_rnbase::load('tx_cfcleague_models_Team');
			$team = tx_cfcleague_models_Team::getInstance($teamId);
			if(is_object($team) && $team->isValid())
				$viewData->offsetSet('team', $team);
		}
		return null;
	}

	private function findData($parameters, $configurations, $viewData, $type) {
		$srv = tx_t3sportstats_util_ServiceRegistry::getStatisticService();
		$confId = $this->getConfId().$type.'.';
		$filter = tx_rnbase_filter_BaseFilter::createFilter($parameters, $configurations, $viewData, $confId);

		$fields = array();
		$options = array('enablefieldsoff' => 1);
		$filter->init($fields, $options);
		$debug = $configurations->get($this->getConfId().'options.debug');
		if($debug)
			$options['debug'] = 1;

		self::handlePageBrowser($configurations, 
			$confId.'data.pagebrowser', $viewData, $fields, $options, array(
			'searchcallback'=> array($srv, 'searchPlayerStats'),
			'pbid' => $type.'ps',
			)
		);

		$items = $srv->searchPlayerStats($fields, $options);
		return $items;
	}
	/**
	 * Pagebrowser vorbereiten. Für die Statistik benötigen wir eine spezielle Anfrage zu Ermittlung der Listenlänge
	 *
	 * @param string $confid Die Confid des PageBrowsers. z.B. myview.org.pagebrowser ohne Punkt!
	 * @param tx_rnbase_configurations $configurations
	 * @param array_object $viewdata
	 * @param array $fields
	 * @param array $options
   */
	private static function handlePageBrowser(&$configurations, $confid, &$viewdata, &$fields, &$options, $cfg = array()) {
		$confid .= '.';
		if(is_array($configurations->get($confid))) {
			// Die Gesamtzahl der Items ist entweder im Limit gesetzt oder muss ermittelt werden
			$listSize = intval($options['limit']);
			if(!$listSize) {
				// Mit Pagebrowser benötigen wir zwei Zugriffe, um die Gesamtanzahl der Items zu ermitteln
				$options['count']= 1;
				$oldWhat = $options['what'];
				$options['what'] = 'count(DISTINCT player) AS cnt';
				$searchCallback=$cfg['searchcallback'];
				if(!$searchCallback) throw new Exception('No search callback defined!');
				$listSize = call_user_func($searchCallback, $fields, $options);
				//$listSize = $service->search($fields, $options);
				unset($options['count']);
				$options['what'] = $oldWhat;
			}
			// PageBrowser initialisieren
			$pbId = $cfg['pbid'] ? $cfg['pbid'] : 'pb';
			$pageBrowser = tx_rnbase::makeInstance('tx_rnbase_util_PageBrowser', $pbId);
	  	$pageSize = intval($configurations->get($confid.'limit'));

	  	$pageBrowser->setState($configurations->getParameters(), $listSize, $pageSize);
			$limit = $pageBrowser->getState();
			$options = array_merge($options, $limit);
			if($viewdata)
				$viewdata->offsetSet('pagebrowser', $pageBrowser);
		}
	}

	function getTemplateName() { return 'playerstats';}
	function getViewClassName() { return 'tx_t3sportstats_views_PlayerStats';}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3sportstats/actions/class.tx_t3sportstats_actions_PlayerStats.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3sportstats/actions/class.tx_t3sportstats_actions_PlayerStats.php']);
}

?>