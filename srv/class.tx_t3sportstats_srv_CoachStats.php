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
// require_ once(PATH_ t3lib.'class.t3lib_svbase.php');


/**
 *
 * @author Rene Nitzsche
 */
class tx_t3sportstats_srv_CoachStats extends t3lib_svbase {
	private $types = array();

	/**
	 * Update statistics for a coach
	 *
	 * @param tx_t3sportstats_util_DataBag $dataBag
	 * @param tx_cfcleague_models_Match $match
	 * @param tx_t3sportstats_util_MatchNoteProvider $mnProv
	 * @param boolean $isHome
	 */
	public function indexCoachStats($dataBag, $match, $mnProv, $isHome) {
		// Wir betrachten das Spiel für einen bestimmten Spieler
		$profId = $dataBag->getParentUid();
		$this->indexSimple($dataBag, $mnProv, $isHome);
		$this->indexWinLoose($dataBag, $match, $isHome);
		$this->indexGoals($dataBag, $match, $isHome);
		$this->indexJokerGoals($dataBag, $match, $isHome, $mnProv);
	}
	/**
	 *
	 * @param tx_t3sportstats_util_DataBag $dataBag
	 * @param tx_cfcleague_models_Match $match
	 * @param boolean $isHome
	 * @param tx_t3sportstats_util_MatchNoteProvider $mnProv
	 */
	private function indexJokerGoals($dataBag, $match, $isHome, $mnProv) {
		// Wir benötigen die Events der gesamten Mannschaft
		$notes = $isHome ? $mnProv->getMatchNotesHome() : $mnProv->getMatchNotesGuest();
		$statTypes = tx_t3sportstats_util_Config::getPlayerStatsSimple();
		$goalTypes = $statTypes['goals']['types'];
		foreach($notes As $note) {
			if($this->isType($note->getType(), $goalTypes)) {
				$playerUid = $note->getPlayer();
				$startPlayer = $this->isStartPlayer($playerUid, $match, $isHome);
				if(!$startPlayer)
					$dataBag->addType('goalsjoker', 1);
			}
		}
	}
	/**
	 *
	 * @param tx_t3sportstats_util_DataBag $dataBag
	 * @param tx_cfcleague_models_Match $match
	 * @param boolean $isHome
	 */
	private function indexGoals($dataBag, $match, $isHome) {
		$goals = $isHome ? $match->getGoalsHome() : $match->getGoalsGuest();
		$dataBag->addType('goals', $goals);
		$dataBag->addType($isHome ? 'goalshome':'goalsaway', $goals);
	}
	/**
	 *
	 * @param tx_t3sportstats_util_DataBag $dataBag
	 * @param tx_cfcleague_models_Match $match
	 * @param boolean $isHome
	 */
	private function indexWinLoose($dataBag, $match, $isHome) {
		$dataBag->setType('played', 1);
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
	private function indexSimple($dataBag, $mnProv, $isHome) {
		// Wir benötigen die Events der gesamten Mannschaft
		$notes = $isHome ? $mnProv->getMatchNotesHome() : $mnProv->getMatchNotesGuest();

		if(!$notes || count($notes) == 0) return;
		$data = array();
		$statTypes = tx_t3sportstats_util_Config::getCoachStatsSimple();
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
	/**
	 * @param int $player profile uid
	 * @param tx_cfcleague_models_Match $match
	 * @param boolean $isHome
	 */
	private function isStartPlayer($player, $match, $isHome) {
		$startPlayer = array_flip(t3lib_div::intExplode(',', $isHome ? $match->getPlayersHome() : $match->getPlayersGuest()));
		return array_key_exists($player, $startPlayer);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3sportstats/srv/class.tx_t3sportstats_srv_CoachStats.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3sportstats/srv/class.tx_t3sportstats_srv_CoachStats.php']);
}

?>