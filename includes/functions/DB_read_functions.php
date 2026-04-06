<?php

/**
 * Database Read Functions
 * Functions for reading from the HEMA Scorecard database
 */
require_once "{$_SERVER['DOCUMENT_ROOT']}/includes/pdo.php";

function readOption($type, $id, $optionEnum)
{
    $id = (int)$id;

    switch($type){
        case 'e':
        case 'E':
            $table = 'eventEventOptions';
            $column = 'eventID';
            $optionID = (int)OPTION['E'][$optionEnum];
            break;
        case 't':
        case 'T':
            $table = 'eventTournamentOptions';
            $column = 'tournamentID';
            $optionID = (int)OPTION['T'][$optionEnum];
            break;
        case 'm':
        case 'M':
            $table = 'eventMatchOptions';
            $column = 'matchID';
            $optionID = (int)OPTION['M'][$optionEnum];
            break;
        default:
            $optionID = 0;
    }

    if($optionID == 0){
        return 0;
    }

    $sql = "SELECT optionValue
            FROM {$table}
            WHERE {$column} = {$id}
            AND optionID = {$optionID}";
    return ( (int)mysqlQuery($sql, SINGLE, 'optionValue') );
}

require_once "{$_SERVER['DOCUMENT_ROOT']}/includes/hemaRatings.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/includes/csv.php";
require_once "{$_SERVER['DOCUMENT_ROOT']}/includes/tournament.php";
