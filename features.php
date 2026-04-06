<?php
$pageName = "Welcome to HEMA Scorecard";

include_once('includes/config.php');
require_once('includes/header_functions.php');

$vJ = '?=1.9.11'; // Javascript Version
$vC = '?=1.4.0'; // CSS Version
$adminStatsDisplay = (boolval(ALLOW['EVENT_MANAGEMENT']) || boolval(ALLOW['VIEW_SETTINGS']) || boolval(ALLOW['STATS_EVENT']));

$eventList = [];
$eventsToShow['active'] = [];
$eventsToShow['recent'] = [];
$eventsToShow['published'] = [];
$isAnyEventActive = false;
foreach (getEventListByPublication('date') as $i => $event) {
    $dateDiffStart = compareDates($event['eventStartDate']);
    $dateDiffEnd = compareDates($event['eventEndDate']);

    $event['displayClass'] = "";

    //Events that are scheduled for the current date
    if ($dateDiffStart > -2 && $dateDiffEnd < 2){
        if ($event['eventStatus'] == 'active'){
            $event['displayStatus'] = '<b>ACTIVE</b>';
            $event['displayClass'] = "link-table-active";
        } else {
            $event['displayStatus'] = 'Unpublished';
        }

        $eventsToShow['active'][] = $event;
        $isAnyEventActive = true;
    } elseif ($dateDiffEnd >= 2){
        // Events that are scheduled for fulture dates
        if ($event['eventStatus'] == 'active' || $event['eventStatus'] == 'complete') {
            $event['displayStatus'] = '<b>Published</b>';
        } else {
            $event['displayStatus'] = 'Unpublished';
        }
        $eventsToShow['recent'][] = $event;
    } else {
        // Events that are less than 14 days old
        if ($event['eventStatus'] == 'active'){
            $event['displayStatus'] = '<b>Published</b>';
        } else {
            $event['displayStatus'] = 'Upcoming';
        }
        $eventsToShow['upcoming'][] = $event;
    }

    // Only make the Active tab the default if there are active events to show.
    // Otherwise the Recent tab will be active on page load.
    if ($isAnyEventActive == true){
        $activeClass = " is-active";
        $recentClass = "";
    } else {
        $activeClass = "";
        $recentClass = " is-active";
    }
}

function displayEventTabe($eventList)
{
    ?>
    <table class="display">
        <thead>
            <tr>
                <th>Date
                    <?=tooltip("Y-M-D")?></th>
                <th>Name</th>
                <th>Location</th>
                <th class='hide-for-small-only'>Status</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($eventList as $event): ?>
                <tr onclick="changeEventJs(<?=$event['eventID']?>)" class='link-table <?=$event['displayClass']?>'>
                    <td><?=$event['eventStartDate']?></td>
                    <td><?=getEventName($event['eventID'])?></td>
                    <td><?=$event['countryName']?> (<?=$event['eventCity']?>, <?=$event['eventProvince']?>)</td>
                    <td class='hide-for-small-only'><?=$event['displayStatus']?></td>
                </tr>

            <?php endforeach ?>
        </tbody>
    </table><?php
}
?>

<!doctype html>
<html class="no-js" lang="en" dir="ltr">
    <?php include('includes/head.php'); ?>
<body>
    <div id='page-wrapper' class='grid-container'>
        <?php
        if (isset($lockedTournamentWarning)){
            tournamentLockedAlert($lockedTournamentWarning);
        }

        displayPageAlerts();
        displayEventAnnouncements();
        ?>
    </div>


    <?php
    include('includes/navbar.php');
    include('includes/lower_nav.php');
    include('includes/page_title.php');
    ?>
    <div class="container" style="padding:15px;">

        <div class='grid-x grid-margin-x'>
            <div class='medium-9 cell'>
                <h3>Why choose HEMA Scorecard?</h3>

                You might be skeptical of switching from good ol' reliable pen and paper to tournament software. What can HEMA Scorecard do for you?

                <BR><BR><h5 style='display:inline;'><a href='#math'>Skip the math</a>:</h5>
                Scorecard calculates all of your pool scores and bracket advancements.
                It can even do solo events like cutting! </li>

                <BR><BR><h5 style='display:inline;'><a href='#show'>Show the world</a>:</h5>
                Results from HEMA Scorecard are live online as soon as the scorkeeper inputs them.
                Participants know how they are doing as soon as they can check their phone,
                and folks back home can follow a match blow for blow.


                <BR><BR><h5 style='display:inline;'><a href='#stats'>See the big picture</a>:</h5>
                HEMA Scorecard can give you detailed stats on how your tournaments ran,
                how the fighters fought, and compare across multiple events.


                <BR><BR>Best of all, the price: <strong>Free</strong>. Sold yet? If you are interested in learning more contact
                <span style="unicode-bidi:bidi-override; direction: rtl; text-decoration:underline">
                ac.nilknarfnaes@ameh
                </span>
            </div>
            <div class='medium-3 small-6 cell'>
                <img src="includes/images/logo_square.jpg">
            </div>

        </div>

        <HR>
        <a name='math'></a>
        <h4>Skip the Math</h4>
        <p>
            HEMA Scorecard supports all sorts of tournament formats. Deductive or Full Afterblows, Pools or Brackets,
            or even crazy one hit tournaments with several rounds of pools. The program was designed to be as versitile
            as possible and run as many types of tournaments as possible.
        </p>

        <p>
            <img src='includes/images/promo_table_s.jpg' style="border: 1px solid black;">
        </p>

        <p>
            <i>Have something so crazy HEMA Scorecard can't even handle it?</i> Great! We want to
            hear about it and may make changes to allow you to realize your dream (or nightmare).
        </p>

        <HR>
        <a name='show'></a>
        <h4>Show the World</h4>
        <p>
            HEMA Scorecard lives online, which means that your participants and spectators are no longer in the dark.
            <BR>
            This also means that you don't have to take the time to print pool results and make brackets for people to follow.
            Why? Because it's right there at their fingertips, with no effort on your part. <BR>
            <em>(Yes, <a href='http://hemaratings.com/'>HEMA Ratings</a> can also grab your results directly, without you needing to send them in.)</em>
        </p>


        <img src='includes/images/promo_phone_s.jpg' style="border: 1px solid black;">

        <HR>
        <a name='stats'></a>
        <h4>See the Big Picture</h4>
        <p>
            Maybe you've run the event, partied your heart out, and you are done until next year. Or maybe you want more?
            <BR>
            HEMA Scorecard keeps track of events exchange by exchange, allowing you to get
            a good look under the hood at your event.
        </p>

        <p>
            <strong>Event Summary</strong> - Have a quick look at how all of your tournaments stacked up:<BR>
            <img src='includes/images/tournamentSummary.jpg'  class='black-border'>
        </p>


        <p>
            <strong>Fighter Histories</strong> - Have a look at how fighters have performed over their careers:<BR>
            <img src='includes/images/fighterSummary.jpg' class='black-border'>
        </p>


    </div>

    <?include('includes/footer.php')?>    
</body>
</html>
