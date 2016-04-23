<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2010 Rene Nitzsche (rene@system25.de)
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
// require_ once(PATH_ t3lib.'class.t3lib_svbase.php');

tx_rnbase::load('tx_cfcleague_util_MatchNote');

/**
 *
 * @author Rene Nitzsche
 */
class tx_t3sportstats_srv_PlayerTimeStats extends t3lib_svbase {
	private $types = array();

	/**
	 * Update statistics for a player
	 * playtime, played
	 *
	 * @param tx_t3sportstats_util_DataBag $dataBag
	 * @param tx_cfcleague_models_Match $match
	 * @param tx_t3sportstats_util_MatchNoteProvider $mnProv
	 */
	public function indexPlayerStats($dataBag, $match, $mnProv, $isHome) {
		// Wir betrachten das Spiel für einen bestimmten Spieler
		$profId = $dataBag->getParentUid();
		$notes = $mnProv->getMatchNotes4Profile($profId);
		$startMin = $this->isStartPlayer($profId, $match, $isHome) ? 0 : -1;
		$isEndPlayer = $startMin == 0 ? true : false;
		if($isEndPlayer)
			$dataBag->setType('played', 1);
		$time = 0;

		foreach($notes As $note) {
			if(tx_cfcleague_util_MatchNote::isChangeIn($note) ) {
				$startMin = $note->getMinute();
				$isEndPlayer = true;
				$dataBag->setType('played', 1);
			}
			elseif(tx_cfcleague_util_MatchNote::isChangeOut($note) ||
				tx_cfcleague_util_MatchNote::isCardYellowRed($note) ||
				tx_cfcleague_util_MatchNote::isCardRed($note) ) {
				$time = $note->getMinute() - $startMin + $time;
				$isEndPlayer = false;
				}
		}
		if($isEndPlayer) {
			$time = 90 - $startMin + $time;
			$time = $time ? $time : 1; // Give the player at least 1 minute.
		}
		$dataBag->addType('playtime', $time);
	}
	/**
	 *
	 * @param tx_cfcleague_models_Match $match
	 * @param boolean $isHome
	 */
	private function isStartPlayer($player, $match, $isHome) {
		$startPlayer = array_flip(t3lib_div::intExplode(',', $isHome ? $match->getPlayersHome() : $match->getPlayersGuest()));
		return array_key_exists($player, $startPlayer);
	}

	private function isType($type, $typeList) {
		if(!array_key_exists($typeList, $this->types)) {
			$this->types[$typeList] = array_flip(t3lib_div::intExplode(',', $typeList));
		}
		$types = $this->types[$typeList];
		return array_key_exists($type, $types);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3sportstats/srv/class.tx_t3sportstats_srv_PlayerTimeStats.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3sportstats/srv/class.tx_t3sportstats_srv_PlayerTimeStats.php']);
}

?>