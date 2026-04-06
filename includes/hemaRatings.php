<?php

require_once "{$_SERVER['DOCUMENT_ROOT']}/includes/pdo.php";
function hemaRatings_createEventInfoCsv($eventID, $dir = "exports/")
{
    // Get roster/event information
    $eventID = (int)$eventID;
    if ($eventID == 0){
        setAlert(SYSTEM,"No Event ID in createRosterCsv");
        return;
    }

    $eventInfo = hemaRatings_GetEventInfo($eventID);
    $eventRoster = hemaRatings_GetEventRosterForExport($eventID);
    $fileName = "{$dir}eventInfo.csv";

    // Create the CSV file
    $fp = fopen($fileName, 'w');

    foreach ($eventInfo as $field => $data){
        if ($field == 'organizingSchool'){
            // This is the schoolID, not the school name. Don't export this.
            continue;
        }

        $name = hemaRatings_getFieldDisplayName($field);

        fputs($fp, "{$name},{$data} ");
        fputs($fp, PHP_EOL);

    }

    fclose($fp);

    return $fileName;
}

function hemaRatings_createEventRosterCsv($eventID = null, $dir = "exports/")
{
    // Creates a .csv file with the eventRoster
    // Format: | Name1 | Name2 | Result1 | Result2 | Stage of Tournament |
    // Get roster/event information
    if($eventID == null){$eventID = $_SESSION['eventID'];}
    if($eventID == null){
        setAlert(SYSTEM,"No Event ID in createRosterCsv");
        return;
    }

    $eventRoster = hemaRatings_GetEventRosterForExport($eventID);
    $eventName = getEventName($eventID);
    $fileName = "{$dir}fighters.csv";

    // Create the CSV file
    $fp = fopen($fileName, 'w');

    foreach ($eventRoster as $fields) 
    {
        fputs($fp, getFighterNameSystem($fields['systemRosterID'], 'first').",");
        fputs($fp, $fields['schoolFullName'].",");
        fputs($fp, $fields['countryIso2'].",");
        fputs($fp, ",");
        fputs($fp, $fields['HemaRatingsID']);

        fputs($fp, '');

        fputs($fp, PHP_EOL);
    }

    fclose($fp);

    return $fileName;
}

function hemaRatings_isEventInfoRequired($field)
{
    switch($field){
        case 'eventName':
        case 'eventStartDate':
        case 'countryIso2':
        case 'eventCity':
        case 'socialMediaLink':
        case 'photoLink':
        case 'submitterName':
        case 'submitterEmail':
        case 'eventConform':
        case 'allMatchesFought':
        case 'missingMatches':
            return true;
        default:
            return false;
    }
}

function hemaRatings_lockFieldUntilComplete($field)
{
    switch($field){
        case 'submitterName':
        case 'submitterEmail':
        case 'eventConform':
        case 'allMatchesFought':
        case 'missingMatches':
            return true;
        default:
            return false;
    }
}

function hemaRatings_getFieldDisplayName($field)
{
    switch($field){
        case 'eventName':            return     'Event Name';        break;
        case 'eventStartDate':        return     'Event Start Date';    break;
        case 'countryIso2':            return     'Event Country';    break;
        case 'eventProvince':        return     'Event State/Province';    break;
        case 'eventCity':            return     'Event City';    break;
        case 'organizingSchool':    return     'Organizing School';    break;
        case 'schoolName':            return     'Organizing School';    break;
        case 'socialMediaLink':        return     'Social Media Link';    break;
        case 'photoLink':            return     'Photo Link';    break;
        case 'submitterName':        return     'Submitter Name';    break;
        case 'submitterEmail':        return     'Submitter E-mail';    break;
        case 'organizerName':        return     'Organizer Name';    break;
        case 'organizerEmail':        return     'Organizer E-mail';    break;
        case 'eventConform':        return     'Does the event conform to the HEMA Ratings event criteria?';    break;
        case 'allMatchesFought':    return "Are there any fights in the submitted results that didn't happen?"; break;
        case 'missingMatches':        return 'Are there any missing fights in the data?';    break;
        case 'notes':                return 'Additional notes';    break;
        default:
            return null;
    }

}

function hemaRatings_isEventInfoComplete($eventID, $hemaRatingInfo = null)
{
    if($hemaRatingInfo == null){
        $hemaRatingInfo = hemaRatings_GetEventInfo($eventID);
    }

    foreach($hemaRatingInfo as $field => $value){
        if(hemaRatings_isEventInfoRequired($field) == false){
            continue;
        }

        if($value == null){
            return false;
        }
    }

    return true;
}



function hemaRatings_getSystemCount()
{
    $formatID = (int)FORMAT_MATCH;
    $sql = "SELECT COUNT(DISTINCT(systemRosterID)) as num
            FROM eventTournamentRoster
            INNER JOIN eventTournaments USING(tournamentID)
            INNER JOIN eventRoster USING(rosterID)
            WHERE formatID = {$formatID}";
    $inSystem['total'] = (int)mysqlQuery($sql, SINGLE, 'num');

    $sql = "SELECT COUNT(DISTINCT(systemRosterID)) as num
            FROM eventTournamentRoster
            INNER JOIN eventTournaments USING(tournamentID)
            INNER JOIN eventRoster USING(rosterID)
            INNER JOIN systemRoster USING(systemRosterID)
            WHERE formatID = {$formatID}
            AND HemaRatingsID IS NOT NULL";
    $inSystem['rated'] = (int)mysqlQuery($sql, SINGLE, 'num');
    $inSystem['unrated'] = $inSystem['total'] - $inSystem['rated'];

    return $inSystem;
}

function hemaRatings_getUnrated()
{
    $formatID = (int)FORMAT_MATCH;
    $sql = "SELECT DISTINCT(systemRosterID), sR.schoolID, countryName,
                schoolShortName, firstName
            FROM eventTournamentRoster
            INNER JOIN eventTournaments USING(tournamentID)
            INNER JOIN eventRoster USING(rosterID)
            INNER JOIN systemRoster AS sR USING(systemRosterID)
            INNER JOIN systemSchools ON sR.schoolID = systemSchools.schoolID
            INNER JOIN systemCountries USING(countryIso2)
            WHERE formatID = {$formatID}
            AND HemaRatingsID IS NULL
            ORDER BY schoolShortName ASC, firstName ASC";
    return mysqlQuery($sql, ASSOC);
}

function hemaRatings_GetEventInfo($eventID)
{
    $eventID = (int)$eventID;

    $sql = "SELECT CONCAT(eventName, ' ', eventYear) AS eventName, eventStartDate,
                countryIso2,eventProvince, eventCity,
                (    SELECT schoolFullName
                    FROM systemSchools
                    WHERE schoolID = organizingSchool) AS schoolName,
                socialMediaLink, photoLink, submitterName, submitterEmail,
                organizerName, organizerEmail,
                eventConform, allMatchesFought, missingMatches, notes,
                organizingSchool
            FROM systemEvents
            LEFT JOIN eventHemaRatingsInfo USING(eventID)
            LEFT JOIN eventSettings USING(eventID)
            WHERE eventID = {$eventID}";
    return mysqlQuery($sql, SINGLE);
}

function hemaRatings_GetEventRosterForExport($eventID)
{
    $eventID = (int)$eventID;

    if($eventID == 0){
        setAlert(SYSTEM,"No eventID in hemaRatings_GetEventRosterForExport()");
        return;
    }

    // The schoolID in eventRoster and systemRoste may not be the same
    // School in event is what they were at the time of the event, in
    // the system it is the school from the latest appearance
    $sql = "SELECT sys.systemRosterID, sch.schoolFullName, sch.countryIso2, sys.HemaRatingsID
            FROM eventRoster ev
            INNER JOIN systemRoster sys ON sys.systemRosterID = ev.systemRosterID
            INNER JOIN systemSchools sch ON ev.schoolID = sch.schoolID
            WHERE ev.eventID = {$eventID}";
    return mysqlQuery($sql, ASSOC);
}

function hemaRatings_getFighterID($systemRosterID)
{
    $systemRosterID = (int)$systemRosterID;

    $sql = "SELECT HemaRatingsID
            FROM systemRoster
            WHERE systemRosterID = {$systemRosterID}";
    return mysqlQuery($sql, SINGLE, 'HemaRatingsID');
}

function hemaRatings_getFighterIDfromRosterID($rosterID)
{
    $rosterID = (int)$rosterID;

    $sql = "SELECT HemaRatingsID
            FROM eventRoster
            INNER JOIN systemRoster USING(systemRosterID)
            WHERE rosterID = {$rosterID}";
    return mysqlQuery($sql, SINGLE, 'HemaRatingsID');

}