<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2014 Rene Nitzsche (rene@system25.de)
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


/**
 * 
 * @author Rene Nitzsche
 */
class tx_t3sportstats_srv_PlayerStats extends t3lib_svbase {
	private $types = array();

	/**
	 * Update statistics for a player
	 *
	 * @param tx_t3sportstats_util_DataBag $dataBag
	 * @param tx_cfcleague_models_Match $match
	 * @param tx_t3sportstats_util_MatchNoteProvider $mnProv
	 * @param boolean $isHome
	 */
	public function indexPlayerStats($dataBag, $match, $mnProv, $isHome) {
		// Wir betrachten das Spiel für einen bestimmten Spieler
		$profId = $dataBag->getParentUid();
		$this->indexSimple($dataBag, $mnProv);
		$this->indexWinLoose($dataBag, $match, $isHome);
	}
	/**
	 * 
	 * @param tx_t3sportstats_util_DataBag $dataBag
	 * @param tx_cfcleague_models_Match $match
	 * @param boolean $isHome
	 */
	private function indexWinLoose($dataBag, $match, $isHome) {
		$toto = $match->getToto();
		$type = 'draw';
		if($toto == 1 && $isHome || $toto == 2 && !$isHome)
			$type = 'win';
		elseif($toto == 2 && $isHome || $toto == 1 && !$isHome)
			$type = 'loose';
		$dataBag->addType($type, 1);
	}
	/**
	 *
	 * @param tx_t3sportstats_util_DataBag $dataBag
	 * @param tx_t3sportstats_util_MatchNoteProvider $mnProv
	 */
	private function indexSimple($dataBag, $mnProv) {
		$profId = $dataBag->getParentUid();
		// Wir benötigen die Events des Spielers
		$notes = $mnProv->getMatchNotes4Profile($profId);

		if(!$notes || count($notes) == 0) return;
		$data = array();
		$statTypes = tx_t3sportstats_util_Config::getPlayerStatsSimple();
		foreach($notes As $note) {
			foreach($statTypes As $type => $info) {
				// Entspricht die Note dem Type in der Info
				if($this->isType($note->getType(), $info['types'])) {
					$dataBag->addType($type, 1);
				}
			}
		}
	}
	private function isType($type, $typeList) {
		if(!array_key_exists($typeList, $this->types)) {
			$this->types[$typeList] = array_flip(t3lib_div::intExplode(',', $typeList));
		}
		$types = $this->types[$typeList];
		return array_key_exists($type, $types);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3sportstats/srv/class.tx_t3sportstats_srv_PlayerStats.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3sportstats/srv/class.tx_t3sportstats_srv_PlayerStats.php']);
}

?>