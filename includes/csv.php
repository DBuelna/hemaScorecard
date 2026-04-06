<?php


function ferrotas_createTournamentResultsCsv($tournamentID, $dir = "exports/")
{
    // Creates a .csv file with the results of all tournament matches
    $tournamentID = (int)$tournamentID;

    $bracketPlacings = [];
    $place = 1;

    $sql = "SELECT groupID, groupName
            FROM eventGroups
            WHERE tournamentID = {$tournamentID}
            AND groupType = 'elim'";
    $groups = mysqlQuery($sql, KEY, 'groupName');

    $primaryBracketID = $groups['winner']['groupID'];
    $secondaryBracketID = @$groups['loser']['groupID']; // may not exist if there is no loser bracket


    $sql = "SELECT matchID, fighter1ID, fighter2ID, winnerID, bracketPosition, bracketLevel
            FROM eventMatches
            WHERE eventMatches.groupID = {$primaryBracketID}
            AND placeholderMatchID IS NULL
            ORDER BY bracketLevel ASC, bracketPosition ASC";
    $primaryMatches = (array)mysqlQuery($sql, ASSOC);

    $sql = "SELECT matchID, fighter1ID, fighter2ID, winnerID, bracketPosition, bracketLevel
            FROM eventMatches
            WHERE eventMatches.groupID = {$secondaryBracketID}
            AND bracketLevel = 1
            AND bracketPosition = 1
            AND placeholderMatchID IS NULL";
    $bronzeMatch = (array)mysqlQuery($sql, SINGLE);

    if(isset($bronzeMatch['winnerID']) == true){
        $isBronzeMatch = true;
        $bronzeWinnerID = (int)$bronzeMatch['winnerID'];

        if($bronzeWinnerID == $bronzeMatch['fighter1ID']){

            $bronzeLoserID = $bronzeMatch['fighter2ID'];

        } elseif($bronzeWinnerID == $bronzeMatch['fighter2ID']){

            $bronzeLoserID = $bronzeMatch['fighter1ID'];

        } else {

            $bronzeLoserID = 0;
        }

    } else {
        $isBronzeMatch = false;
    }

    $numMatches = sizeof($primaryMatches);
    $losersAtLevel = [];
    foreach($primaryMatches as $index => $match){

        $winnerID = (int)$match['winnerID'];

        if($winnerID == $match['fighter1ID']){

            $loserID = $match['fighter2ID'];

        } elseif($winnerID == $match['fighter2ID']){

            $loserID = $match['fighter1ID'];

        } else {

            $loserID = 0;
        }


        if($match['bracketLevel'] == 1){

            $bracketPlacings[$place] = $winnerID;
            $place++;
            $assignedFighters[$winnerID] = true;

            $bracketPlacings[$place] = $loserID;
            $place++;
            $assignedFighters[$loserID] = true;

            if($isBronzeMatch == true){

                $bracketPlacings[$place] = $bronzeWinnerID;
                $place++;
                $assignedFighters[$bronzeWinnerID] = true;

                $bracketPlacings[$place] = $bronzeLoserID;
                $place++;
                $assignedFighters[$bronzeLoserID] = true;

            }

            continue;

        } elseif ($isBronzeMatch == true && $match['bracketLevel'] == 2){

            continue;
        }

        $losersAtLevel[] = $loserID;


        if(        isset($primaryMatches[$index+1]['bracketLevel']) == false
            ||     $match['bracketLevel'] != $primaryMatches[$index+1]['bracketLevel']){


            $rosterIDs = implode2int($losersAtLevel);

            $sql = "SELECT rosterID
                    FROM eventStandings
                    WHERE rosterID IN ({$rosterIDs})
                    AND tournamentID = {$tournamentID}
                    ORDER BY rank ASC";
            $sortedRosterIDs = (array)mysqlQuery($sql, SINGLES, 'rosterID');

            foreach($sortedRosterIDs as $rosterID){
                $bracketPlacings[$place] = $rosterID;
                $place++;
                $assignedFighters[$rosterID] = true;
            }

            $losersAtLevel = [];
        }

    }

    $rosterIDsPlaced = implode2int($bracketPlacings);

    $sql = "SELECT rosterID
            FROM eventStandings
            WHERE rosterID NOT IN ({$rosterIDsPlaced})
            AND tournamentID = {$tournamentID}
            ORDER BY rank ASC";
    $sortedRosterIDs = (array)mysqlQuery($sql, SINGLES, 'rosterID');

    foreach($sortedRosterIDs as $rosterID){
        $bracketPlacings[$place] = $rosterID;
        $place++;
    }

    // Create the CSV file
    $tournamentName = getTournamentName($tournamentID);
    $fileName = "{$dir}{$tournamentName}.csv";
    $fp = fopen($fileName, 'w');

    foreach($bracketPlacings as $place => $rosterID){

        $name = getFighterName($rosterID);
        $schoolName = getFighterSchoolName($rosterID);

        $fields = [$place, $name, $schoolName];

        $comma = ',';

        foreach($fields as $index => $field){
            if ($index == sizeof($fields)-1){
                $comma = null;
            }
            fputs($fp, $field.$comma);
        }
        fputs($fp, PHP_EOL);

    }
    fclose($fp);

    return $fileName;

}

function hemaRatings_createTournamentResultsCsv($tournamentID, $dir = "exports/")
{
    // Creates a .csv file with the results of all tournament matches
    // Format: | Name1 | Name2 | Result1 | Result2 | Stage of Tournament |
    // Get information about the tournament
    if($tournamentID == null){
        setAlert(SYSTEM,"No tournamentID in exportTournament()");
        return;
    }
    $tournamentName = getTournamentName($tournamentID);

    $sql = "SELECT eventID
            FROM eventTournaments
            WHERE tournamentID = {$tournamentID}";
    $eventID = mysqlQuery($sql, SINGLE, 'eventID');
    $eventName = getEventName($eventID);

    $fileName = "{$dir}{$tournamentName}.csv";

    // Get the match results
    $sql = "SELECT matchID, winnerID, fighter1ID, fighter2ID, ignoreMatch,
                (    SELECT exchangeType
                    FROM eventExchanges AS eE2
                    WHERE eE2.matchID = eM.matchID
                    AND (exchangeType = 'winner' OR exchangeType = 'doubleOut' OR exchangeType = 'tie')
                    LIMIT 1) AS endExchangeType
            FROM eventMatches AS eM
            INNER JOIN eventGroups USING(groupID)
            WHERE tournamentID = {$tournamentID}
            AND placeholderMatchID IS NULL
            AND matchComplete = 1";
    $finishedMatches = mysqlQuery($sql, ASSOC);

    $sql = "SELECT MAX(groupSet) AS numGroupSets
            FROM eventGroups
            WHERE tournamentID = {$tournamentID}";
    $numGroupSets = (int)mysqlQuery($sql, SINGLE, 'numGroupSets');

    // Create the CSV file
    $fp = fopen($fileName, 'w');

    foreach($finishedMatches as $match){

        $winnerID = (int)$match['winnerID'];

        if($winnerID == $match['fighter1ID']){
            $f1ID = $match['fighter1ID'];
            $f2ID = $match['fighter2ID'];
            $f1Result = 'Win';
            $f2Result = 'Loss';
        } elseif ($winnerID == $match['fighter2ID']){
            $f1ID = $match['fighter2ID'];
            $f2ID = $match['fighter1ID'];
            $f1Result = 'Win';
            $f2Result = 'Loss';
        } else {
            $f1ID = $match['fighter1ID'];
            $f2ID = $match['fighter2ID'];

            if($match['endExchangeType'] == 'doubleOut'){
                $f1Result = 'Loss';
            } else {
                $f1Result = 'Draw';
            }

            $f2Result = $f1Result;
        }

        $fighter1 = getFighterName($f1ID, null, 'first');
        $fighter2 = getFighterName($f2ID, null, 'first');

        $stageName = (string)getMatchStageName($match['matchID'], $numGroupSets);

        if((int)$match['ignoreMatch'] == 1){
            $stageName = "!! Attention !! Excluded From Scoring Calculations ".$stageName;
        }

        $fields = [$fighter1, $fighter2, $f1Result, $f2Result, $stageName];

        $comma = ',';

        foreach($fields as $index => $field){
            if ($index == sizeof($fields)-1){
                $comma = null;
            }
            fputs($fp, $field.$comma);
        }
        fputs($fp, PHP_EOL);

    }
    fclose($fp);

    return $fileName;
}
