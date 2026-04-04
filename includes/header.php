<?php
include_once('includes/config.php');
require_once('includes/header_functions.php');

$vJ = '?=1.9.11'; // Javascript Version
$vC = '?=1.4.0'; // CSS Version

$adminStatsDisplay = (boolval(ALLOW['EVENT_MANAGEMENT']) || boolval(ALLOW['VIEW_SETTINGS']) || boolval(ALLOW['STATS_EVENT']));
?>
<!doctype html>
<html class="no-js" lang="en" dir="ltr">

<?php include('includes/head.php'); ?>

<body>

<?php 
include('includes/navbar.php');
include('includes/page_title.php');
include('includes/lower_nav.php');
?>

<div id='page-wrapper' class='grid-container'>
    <?php
    if (isset($lockedTournamentWarning)){
        tournamentLockedAlert($lockedTournamentWarning);
    }

    displayPageAlerts();
    displayEventAnnouncements();
    ?>
</div>