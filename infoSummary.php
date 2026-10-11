<?php
/*******************************************************************************
	Info Summary

	Displays all the tournament medalists
	Login:
		- ADMIN or above can add/remove final medalists

*******************************************************************************/

// INITIALIZATION //////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

$pageName = "Final Results";
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/pageFunctions.php';
require_once __DIR__ . '/includes/summaryFunctions.php';

if ($_SESSION['eventID'] != null) {

	define("NUM_FINALISTS_DISPLAYED",4);
	$tournamentList = getTournamentsFull($_SESSION['eventID']);
	$tournamentList = sortTournamentsForPlacings($tournamentList);
	$anyFinalized = areAnyTournamentsFinalized($_SESSION['eventID']);

	if(!isset($_SESSION['manualTournamentPlacing'])){
		$_SESSION['manualTournamentPlacing'] = '';
	}

	if(ALLOW['EVENT_SCOREKEEP'] == true || ALLOW['EVENT_MANAGEMENT'] == true){
		define('ALLOW_EDITING',true);
	} else {
		define('ALLOW_EDITING',false);
	}

	$tournamentsToDisplay = sortTournamentAndDivisions($_SESSION['eventID']);

	foreach((array)$tournamentList as $tournamentID => $data){
		$tournamentList[$tournamentID]['name'] = getTournamentName($tournamentID);
		$tournamentList[$tournamentID]['link'] = "onclick='javascript:document.goToTournamentAlt{$tournamentID}.submit();'
					style='cursor: pointer;'";
	}

	foreach($tournamentsToDisplay as $j => $t){

		if(isset($t['divisionID']) == true){

			if(isset($t['tournaments']) == false){
				unset($tournamentsToDisplay[$j]);
				continue;
			}

			$firstTab = true;

			foreach((array)$t['tournaments'] as $i => $item){

				$tID = $item['tournamentID'];

				$numChars = strlen($t['tournaments'][$i]['shortName']);

				$size = 1 - 0.02 * ($numChars - 10);

				$size = min(1, $size);
				$size = max($size, 0.6);
				$tournamentsToDisplay[$j]['tournaments'][$i]['tabTextSize'] = $size;

				$tournamentList[$tID]['placings'] = getTournamentPlacings($tID);

				$tournamentList[$tID]['tabClass'] = "";
				$tournamentList[$tID]['bodyClass'] = " ";

				if(count($tournamentList[$tID]['placings']) == 0){
					$tournamentList[$tID]['tabClass'] .= 'unfinished';
				}

				if($firstTab == true){
					$tournamentList[$tID]['tabClass'] .= " selected";
					$firstTab = false;
				} else {
					$tournamentList[$tID]['bodyClass'] .= " hidden";
				}

			}

		}
	}
}

$contentTemplate = __DIR__ . '/includes/views/summary.php';
include __DIR__ . '/includes/layout.php';
