<?php

/******************************************************************************/

function debugging(){

    if(defined("SHOW_POST") && SHOW_POST === true){

        if(isset($_SESSION['urlNav']) && defined("SHOW_URL_NAV") && SHOW_URL_NAV === true){

            echo "---- URL_NAV ----------------------------------------------------";
            show($_SESSION['urlNav']);

        } else {
            echo "---- POST -------------------------------------------------------";
            show($_SESSION['post']);
        }
    }

    unset($_SESSION['post']);
    unset($_SESSION['urlNav']);

    if(defined("SHOW_SESSION") && SHOW_SESSION === true){
        echo "---- SESSION ----------------------------------------------------";
        show($_SESSION);
    }

}

function DisplayServerVersion()
{
    switch(DEPLOYMENT){
        case DEPLOYMENT_PRODUCTION: { return; break; } // don't display anything
        case DEPLOYMENT_LOCAL:         {$color="#39FF14";    $text = "Local Server";    break;}
        case DEPLOYMENT_TEST:         {$color="#FFAD00";    $text = "Test Server";    break;}
        case DEPLOYMENT_UNKNOWN:
        default:                    {$color="#FF69B4";    $text = "Unknown Deployment";    break;}
    }?>

    <div class='text-center' style='font-size:0.7em;background-color: <?=$color?>;'>
        <i><?=$text?></i>
    </div><?php
}

function menuEvent()
{
    if ($_SESSION['eventID'] == null){
        return;
    }

    if (
        ALLOW['EVENT_MANAGEMENT'] == false &&
        ALLOW['VIEW_SETTINGS'] == false &&
        ALLOW['STATS_EVENT'] == false &&
        isAnyEventInfoViewable() == false) {
        return;
    }

    $isInstructors = logistics_isEventInstructors($_SESSION['eventID']);
    $faq = getEventFaq($_SESSION['eventID']);
    ?>
    <li>
        <a href='#'>Event Information</a>
        <ul class='menu vertical'>
            <li><a href='infoSummary.php?t=0'>Information/Results</a></li>
            <li><a href='participantsEvent.php?t=0'>Event Roster</a></li>
            <li><a href='logisticsSchedule.php?t=0'>Schedule</a></li>
            <li><a href='infoRules.php?t=0'>Tournament Rules</a></li>

            <li>
                <a href='#'>Event Stats</a>
                <ul class='menu vertical'>
                    <li><a href='statsEvent.php?t=0'>Attendance/Schools</a></li>
                    <li><a href='statsTournaments.php?t=0'>Tournament Exchanges</a></li>
                    <li><a href='statsWorkshops.php?t=0'>Workshops</a></li>
                </ul>
            </li>

            <li>
                <a href='#'>Custom Schedules</a>
                <ul class='menu vertical'>
                    <li><a href='logisticsSchedule.php?t=0'>Main Schedule</a></li>
                    <li><a href='participantsFiltered.php?t=0'>Matches by School</a></li>
                    <li><a href='infoScheduleWorkshops.php?t=0'>Class Schedule</a></li>
                    <li><a href='participantsSchedules.php?t=0'>Individual Schedules</a></li>
                </ul>
            </li>

            <?php if($isInstructors == true): ?>
                <li><a href='logisticsInstructors.php?t=0'>Event Instructors</a></li>
            <?php endif ?>

            <?php if(ALLOW['EVENT_MANAGEMENT'] == true || ($faq != [] && ALLOW['VIEW_RULES'] == true)): ?>
                <li><a href='infoFaq.php?t=0'><b>Event FAQ</b></a></li>
            <?php endif ?>

        </ul>
    </li><?php
}

function menuTournament()
{
    if ($_SESSION['tournamentID'] == null) {
        return;
    }
    ?>
    <li>
        <a href='#'>Tournament Information</a>
        <ul class='menu vertical'>
            <li><a href='participantsTournament.php'>Roster</a></li>
            <li><a href='videoMatchList.php'>Video Links</a></li>

            <?php if( $_SESSION['formatID'] == FORMAT_MATCH): ?>
                <li><a href='statsFighterSummary.php'>Exchanges By Fighter</a></li>
            <?php endif ?>

            <?php if(ALLOW['EVENT_MANAGEMENT'] == true): ?>

                <li><div class="drop-down-separator">During</div></li>
                <li><a href='adminFighters.php'>Withdraw Fighters</a></li>

                <li><div class="drop-down-separator">Before</div></li>
                <li><a href='adminTournaments.php'>
                    Settings for: <b><?=getTournamentName($_SESSION['tournamentID'])?></b></a>
                </li>
                <li><a href='participantsRatings.php'>Fighter Ratings</a></li>
                <li><a href='statsScoresheets.php'>Scoresheets</a></li>

            <?php endif ?>

        </ul>
    </li><?php
}

function menuEventOrg()
{
    if (ALLOW['EVENT_MANAGEMENT'] == false && ALLOW['VIEW_SETTINGS'] == false){
        return;
    }

    if($_SESSION['eventID'] == 0){
        return;
    }

    $eventDates = getEventDates($_SESSION['eventID']);

    if (compareDates($eventDates['eventStartDate']) < 0){
        $menuOrder = ['menuEventOrgBefore','menuEventOrgDuring','menuEventOrgAfter'];
    } elseif (compareDates($eventDates['eventEndDate']) <= 0) {
        $menuOrder = ['menuEventOrgDuring','menuEventOrgBefore','menuEventOrgAfter'];
    } else {
        $menuOrder = ['menuEventOrgAfter','menuEventOrgDuring','menuEventOrgBefore'];
    }
    ?>
    <li>
        <a href='#'>Event Organization</a>
        <ul class='menu vertical'>

            <?php
                foreach($menuOrder as $functionName){
                    call_user_func($functionName);
                }
            ?>

        </ul>
    </li><?php
}

function menuEventOrgBefore()
{ ?>
    <div class="drop-down-separator">Before</div>
        <li>
            <a href='#'><b>Event Settings</b></a>
            <ul class='menu vertical'>
                <li><a href='adminEvent.php?t=0'><b>Event Settings</b></a></li>
                <li><a href='adminDivisions.php?t=0'>Tournament Divisions</a></li>
                <li><a href='adminBurgees.php?t=0'>School Standings</a></li>
            </ul>
        </li>
        <li><a href='adminNewTournaments.php?t=0'><b>Create New Tournament</b></a></li>
        <li>
            <a href='#'>Schedule</a>
            <ul class='menu vertical'>
                <li><a href='logisticsSchedule.php?t=0'>Edit Schedule</a></li>
                <li><a href='logisticsLocations.php?t=0'>Edit Locations</a></li>
                <li><a href='logisticsScheduleOverlap.php?t=0'>Tournament Overlap Checker</a></li>
            </ul>
        </li>
        <?php if(ALLOW['SOFTWARE_ADMIN'] == true): ?>
            <li><a href='adminSponsors.php?t=0'>Sponsors</a></li>
        <?php endif ?>
        <li>
            <a href='#'>Staffing</a>
            <ul class='menu vertical'>

                <div class="drop-down-separator">Setup</div>
                <li><a href='logisticsStaffRoster.php?t=0'>Staff Roster</a></li>
                <li><a href='logisticsInstructors.php?t=0'>Instructors</a></li>
                <li><a href='logisticsStaffShifts.php?t=0'>Shifts</a></li>
                <li><a href='logisticsStaffTemplates.php'>Shift Templates</a></li>

                <div class="drop-down-separator">Summary</div>
                <li><a href='logisticsParticipantHours.php?t=0'>Hours</a></li>
                <li><a href='logisticsStaffConflicts.php?t=0'>Conflicts</a></li>
                <li><a href='logisticsStaffGrid.php?t=0'>Full Grid</a></li>
                <li><a href='logisticsStaffList.php'>Full Staff List</a></li>
                <li><a href='logisticsStaffMatch.php'>Match Staff Lists</a></li>
                <li><a href='logisticsJudgeEval.php'>Judge Summary</a></li>

            </ul>
        </li>
    </div><?php
}

function menuEventOrgDuring()
{ ?>
    <li><div class="drop-down-separator">During</div></li>
    <li><a href='logisticsAnnouncements.php?t=0'>Announcements</a><li>
    <li><a href='participantsCheckIn.php?t=0'>Check-In Participants</a></li>
    <li><a href='adminFighterPenalties.php?t=0'>Penalties By Fighter</a></li>
    <li><a href='videoLivestream.php?t=0'>Livestream</a></li>
    <li>
        <a href='#'>Views</a>
        <ul class='menu vertical'>
            <li><a href='infoRingAssignments.php'>Fighter Pool Assignment</a></li>
            <li><a href='infoLocationMatchQueue.php'>Match Queue by Location</a></li>
            <li><a href='logisticsStaffList.php'>Full Staff List</a></li>
        </ul>
    </li><?php
}

function menuEventOrgAfter()
{
    if (ALLOW['EVENT_MANAGEMENT'] == false){
        return;
    }
    ?>
    <div class="drop-down-separator">After</div>
    <li><a href='statsResultsDump.php?t=0'>Export Results</a></li><?php
}

function menuAnalytics()
{
    ?>
    <li class='white-text'>
        <a href='#'>Stats/Analytics</a>
        <ul class='menu vertical'>
            <li><a href='statsResultsDump.php?t=0'>Export Results</a></li>
            <li>
                <a href='#'>Logistics/Schedule</a>
                <ul class='menu vertical'>
                    <li><a href='statsMatchLength.php?t=0'>Match Timings</a></li>
                    <li><a href='statsScheduleAssistant.php?t=0'>Tournament Time Calculator</a></li>
                </ul>
            </li>
            <li>
                <a href='#'>Roster</a>
                <ul class='menu vertical'>
                    <li><a href='participantsAttendance.php?t=0'>Attendance By Fighter</a></li>
                    <li><a href='participantsSystem.php?t=0'>Full System Roster</a></li>
                </ul>
            </li>
            <li>
                <a href='#'>Events/System</a>
                <ul class='menu vertical'>
                    <li><a href='statsYear.php?t=0'>Annual Summary</a></li>
                    <li><a href='statsTournamentTypes.php?t=0'>All Tournaments By Type</a></li>
                    <li><a href='statsPlacings.php?t=0'>Placings By Country</a></li>
                </ul>
            </li>
            <?php if (boolval(ALLOW['STATS_ALL'])) { ?>
                <li>
                    <a href='#'>DEVEL Views</a>
                    <ul class='menu vertical'>
                        <li><a href='statsFighters.php?t=0'>Fighter Histories</a></li>
                        <li><a href='statsMultiEvent.php?t=0'>Exchange Types By Weapon</a></li>
                        <li><a href='statsIndividual.php?t=0'>Fighter Exchanges By Filter</a></li>
                    </ul>
                </li>
            <?php } ?>
        </ul>
    </li><?php
}

function menuAdmin()
{
    if(ALLOW['SOFTWARE_ADMIN'] == false && ALLOW['SOFTWARE_ASSIST'] == false){
        if (
            $_SESSION['userName'] != null &&
            $_SESSION['userName'] != 'eventStaff' && $_SESSION['userName'] != 'eventOrganizer'
        ) {
            echo "<li><a href='masterPasswords.php'>Change Password</a></li>";
        }

        return;
    } ?>
    <li class="white-text">
        <a href='#'>ADMIN</a>
        <ul class='menu vertical'>
            <li><a href='masterEvents.php?t=0'>Manage Events</a></li>
            <li>
                <a href='#'>Database</a>
                <ul class='menu vertical'>
                    <li><a href='adminSchools.php?t=0'>Edit School List</a></li>
                    <li><a href='masterTournamentTypes.php?t=0'>Tournament Types</a></li>
                    <li><a href='cutQuals.php'>Cutting Qualifications</a></li>
                </ul>
            </li>

            <?php if(  ALLOW['SOFTWARE_ADMIN'] == true): ?>
            <li>
                <a href='#'>Data Integrity</a>
                <ul class='menu vertical'>
                    <li><a href='masterHemaRatings.php?t=0'>HEMA Ratings</a></li>
                    <li><a href='masterDuplicates.php?t=0'>Duplicate Names</a></li>
                </ul>
            </li>
            <?php endif ?>
            <li><a href='masterPasswords.php?t=0'>Change Password</a></li>

        </ul>
    </li><?php
}

function activeLivestream()
{
    if((isVideoStreamingForEvent($_SESSION['eventID']) == false) || ALLOW['VIEW_MATCHES'] == false){
        return;
    }?>
    <li><a class='button warning hollow no-bottom' href='videoLivestream.php'>Livestream</a></li><?php
}

function displayEventAnnouncements()
{
    $eventID = (int)$_SESSION['eventID'];

    if ($eventID == 0) {
        return;
    }

    if(ALLOW['EVENT_SCOREKEEP'] == true || ALLOW['SOFTWARE_ASSIST'] == true || ALLOW['VIEW_SETTINGS'] == true){
        $showStaffAnnouncements = true;
    } else {
        $showStaffAnnouncements = false;
    }

    if(isset($_SESSION['hideAnnouncement']) == false){
        $_SESSION['hideAnnouncement']= [];
    }

    $announcements     = (array)logistics_getEventAnnouncments($_SESSION['eventID']);
    $currentTime     = time();

    // - Add a warning if there are ties in the standings that can't be resolved --/
    if (ALLOW['EVENT_SCOREKEEP'] == TRUE) {
        $tournamentID = $_SESSION['tournamentID'];
        $tiedFighters = findTiedFighters($tournamentID);
        $poolsActive = isInProgress($tournamentID,'pool');
        $bracketPopulated = isBracketPopulated($tournamentID);

        if ($tiedFighters != [] && $poolsActive == false && $bracketPopulated == false){

            $a['message'] = 'There are ties between the following fighters:<ul>';
            foreach($tiedFighters as $fighter){
                $a['message'] .= "<li>{$fighter}</li>";
            }
            $a['message'] .= "</ul>Scorecard has used all specified tiebreakers
                and can not resolve the results. Place has been assigned randomly,
                please take any necessary measures to break the tie if you intend
                to seed a bracket based on this.";

            $a['announcementID'] = -($tournamentID);

            $a['displayUntil'] = $currentTime + 99999;
            $a['visibility'] = 'staff';

            $announcements[] = $a;

        }
    }

    // -- Display announcements ---------------------------------------------------/
    foreach ($announcements as $a) {
        $timeLeft = $a['displayUntil'] - $currentTime;

        if ($timeLeft <= 0) {
            continue;
        }
        if ($a['visibility'] != 'all' && $showStaffAnnouncements == false) {
            continue;
        }
        if (isset($_SESSION['hideAnnouncement'][$a['announcementID']]) == true) {
            continue;
        }
        ?>
        <div class='cell callout warning' data-closable>
            <b>Announcement</b><BR>
            <?=$a['message']?>

            <form method="POST">
                <input type='hidden' name='announcementID' value='<?=$a['announcementID']?>'>
                <button class='button hollow no-bottom' name='formName' value='hideAnnouncement'>
                    Got it. Stop showing me this.
                </button>
            </form>

            <button class='close-button' aria-label='Dismiss alert' type='button' data-close>
                <span aria-hidden='true'>&times;</span>
            </button>
        </div><?
    }
}

function tournamentLockedAlert($isWarning)
{
    if(!$isWarning){ return; }
    if(LOCK_TOURNAMENT == null){ return; }
    if(ALLOW['EVENT_SCOREKEEP'] == false && ALLOW['EVENT_MANAGEMENT'] == false){ return; }
    ?>

    <div class='callout alert text-center' data-closeable>
        Results for this tournament have been finalized, most changes have been disabled.
        <a href='infoSummary.php#anchor<?=$_SESSION['tournamentID']?>'>Remove final results</a> to edit.
    </div><?php
}

function eventNameForHeader()
{
    // Add the event name or prompty to select an event
    $eventID = $_SESSION['eventID'];
    $page = basename($_SERVER['PHP_SELF']);

    if (
        (ALLOW['SOFTWARE_EVENT_SWITCHING'] == true) ||
        (($_SESSION['userName'] == '')) &&
        (
            ($page == 'statsFighterSummary.php') ||
            ($page == 'statsTournaments.php') ||
            ($page == 'statsEvent.php') )
        ) { ?>
            <form method='POST'>
            <input type='hidden' name='formName' value='selectEvent'>
            <div class='grid-x align-center'>
            <select class='shrink' name='changeEventTo' onchange='this.form.submit()'>
                <?php eventNameListSelectOptions($eventID) ?>
            </select>
            </div>
            </form><?php
    } else if ($_SESSION['eventName'] != null AND $_SESSION['eventName'] != ' ') {
        echo $_SESSION['eventName'];
    } else {
        echo "No Event Selected";
    }
}

function eventNameListSelectOptions($eventID)
{
    if ($eventID == null){
        echo "<option selected disabled>* No Event Selected *</option>";
    }

    $newList = getEventListByPublication('date');
    $allList = getEventListByPublication();

    // This makes it so when tournaments are twice in the list the top option
    // is the one that is selected.
    $notAlreadySelected = 1;

    echo "<option disabled>-- Recent & Upcoming -------------------------------</option>";

    foreach ($newList as $event) {
        if (compareDates($event['eventStartDate']) > 14) {
            continue;
        }

        if($event['eventID'] == $eventID){
            $notAlreadySelected = 0;
        }
        ?>
        <option <?=optionValue($event['eventID'], $eventID)?> >
            &nbsp;&nbsp;<?=$event['eventName']?> <?=$event['eventYear']?>
        </option><?php
    }

    echo "<option disabled>-- Full List ---------------------------------------</option>";

    foreach ($allList as $event){ ?>
        <option <?=optionValue($event['eventID'], $eventID * $notAlreadySelected)?> >
            &nbsp;&nbsp;<?=$event['eventName']?> <?=$event['eventYear']?>
        </option><?php
    }
}

function tournamentListForHeader()
{
    $currentTournamenID    = $_SESSION['tournamentID'];
    $currentTournamentName = '';

    if ($currentTournamenID != null) {
        $currentTournamentName = getTournamentName($currentTournamenID);
    }

    $tournamentsToDisplay = sortTournamentAndDivisions($_SESSION['eventID']);
    ?>

    <li>
        <?php
        if ($_SESSION['tournamentID'] == null) {
            echo "<span class='button success hollow' style='margin-bottom: 0px'>Select Tournament</span>";
        } else {
            echo "<a href='#' class='button hollow title-bar'>{$currentTournamentName}</a>";
        }

        if ($tournamentsToDisplay != []):?>

            <form method='POST' name='goToTournamentForm' id='goToTournamentForm'>
                <input type='hidden' name='formName' value='changeTournament'>
            </form>

            <ul class='menu vertical'>

            <?php foreach($tournamentsToDisplay as $t):?>
            <li>
                <?php if(isset($t['divisionID']) == false):?>
                    <?=headerFormat($t)?>
                <?php else: ?>
                    <a><i><?=$t['name']?></i></a>
                    <ul class='menu vertical'>
                        <?php foreach(@(array)$t['tournaments'] as $item):?>
                            <li><?=headerFormat($item)?></li>
                        <?php endforeach?>
                    </ul>
                <?php endif ?>
            </li>

            <?php endforeach ?>

            </ul>

        <?php endif ?>
    </li><?php
}

function headerFormat($tournament){

    $tournamentID = (int)$tournament['tournamentID'];

    $tournamentName = $tournament['shortName'];

    $t['isInProgress'] = isInProgress($tournamentID);

    $linkClass = '';
    if($t['isInProgress'] == true){
        $linkClass .= 'bold';
    }

    $format = getTournamentFormat($tournamentID);

    $isMeta = ($format == FORMAT_META);
    $isStarted = isFightingStarted($tournamentID, $isMeta);

    $t['landingPage'] = '';

    // If we are already in a tournament and switching to a new one stay on
    // the same page. But if we aren't in a tournament the landing page will
    // be set to whatever makes the most sense based on the current state
    // of the tournament and it's type.
    if($_SESSION['tournamentID'] == null){

        $t['landingPage'] = 'participantsTournament.php';

        if(ALLOW['VIEW_MATCHES'] == false){
            $format = FORMAT_NONE;
        }

        switch($format){
            case FORMAT_SOLO:{

                if($isStarted == true){
                    $t['landingPage'] = 'roundStandings.php';
                } else {
                    $t['landingPage'] = 'roundRosters.php';
                }

                break;
            }
            case FORMAT_META:{

                if($isStarted == true){
                    $t['landingPage'] = 'poolStandings.php';
                } else {
                    $t['landingPage'] = 'participantsComponents.php';
                }

                break;
            }
            case FORMAT_MATCH:{

                if (isBracketPopulated($tournamentID) == true){
                    $t['landingPage'] = 'finalsBracket.php';
                } elseif ($isStarted == true){
                    $t['landingPage'] = 'poolMatches.php';
                } elseif (isPools($tournamentID) == true){
                    $t['landingPage'] = 'poolRosters.php';
                } else {
                    $t['landingPage'] = 'participantsTournament.php';
                }

                break;
            }
            case FORMAT_RESULTS:{

                $t['landingPage'] = 'infoSummary.php';
                break;

            }
            default: {

                $t['landingPage'] = 'participantsTournament.php';
                break;

            }
        }

    }

    $linkText = "<a class='{$linkClass}' onclick=\"changeTournamentJs({$tournamentID},'{$t['landingPage']}')\">";
    $linkText .= $tournamentName;
    $linkText .= "</a>";

    return ($linkText);
}
