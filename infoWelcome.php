<?php
/*******************************************************************************
    Event Selection

    Select which event to use
    Login:
        - SUPER ADMIN can see hidden events

*******************************************************************************/

// INITIALIZATION //////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/pageFunctions.php';

$pageName = 'Welcome to HEMA Scorecard';

$eventList = getEventListByPublication('date');
$eventsToShow['active'] = [];
$eventsToShow['recent'] = [];
$eventsToShow['upcoming'] = [];

$isAnyEventActive = false;

foreach ($eventList as $i => $event) {
    $dateDiffStart = compareDates($event['eventStartDate']);
    $dateDiffEnd = compareDates($event['eventEndDate']);

    if($dateDiffEnd > 14){ continue; }

    $event['displayClass'] = "";


    if($dateDiffStart > -2 && $dateDiffEnd < 2){

        // Events that are scheduled for the current date

        if($event['eventStatus'] == 'active'){
            $event['displayStatus'] = '<b>ACTIVE</b>';
            $event['displayClass'] = "link-table-active";
        } else {
            $event['displayStatus'] = 'Unpublished';
        }

        $eventsToShow['active'][] = $event;
        $isAnyEventActive = true;

    } elseif ($dateDiffEnd >= 2){

        // Events that are scheduled for fulture dates

        if($event['eventStatus'] == 'active' || $event['eventStatus'] == 'complete'){
            $event['displayStatus'] = '<b>Published</b>';
        } else {
            $event['displayStatus'] = 'Unpublished';
        }

        $eventsToShow['recent'][] = $event;

    } else {

        // Events that are less than 14 days old

        if($event['eventStatus'] == 'active'){
            $event['displayStatus'] = '<b>Published</b>';
        } else {
            $event['displayStatus'] = 'Upcoming';
        }

        $eventsToShow['upcoming'][] = $event;

    }

}

// Only make the Active tab the default if there are active events to show.
// Otherwise the Recent tab will be active on page load.
if ($isAnyEventActive == true) {
    $activeClass = " is-active";
    $recentClass = "";
} else {
    $activeClass = "";
    $recentClass = " is-active";
}

$contentTemplate = __DIR__ . '/includes/views/welcome.php';
include __DIR__ . '/includes/layout.php';
