<?php
/*******************************************************************************
    Page Header

    Links to stylesheets and config as well as header content
    and navigation links
    LOGIN:
        - Menu items change depending on login level
        - Login button becomes 'Log Out' when loged in

*******************************************************************************/

include_once('includes/config.php');

require_once __DIR__ . '/pageFunctions.php';

$vJ = '?=1.9.11'; // Javascript Version
$vC = '?=1.4.0'; // CSS Version

if(    ALLOW['EVENT_MANAGEMENT'] == true
    || ALLOW['VIEW_SETTINGS'] == true
    || ALLOW['STATS_EVENT'] == true){

    $adminStatsDisplay = true;
} else {
    $adminStatsDisplay = false;
}
?>

<!doctype html>
<html class="no-js" lang="en" dir="ltr">

<head>
<?php include __DIR__ . '/partials/head.php'; ?>
</head>

<body>

<?php include __DIR__ . '/partials/navbar.php'; ?>

<?php include __DIR__ . '/partials/page-title.php'; ?>

<!-- START Lower Navigation --------------------------------------->
<div id='page-wrapper' class='grid-container'>
<?php include __DIR__ . '/partials/tournament-nav.php'; ?>
<!-- END Lower Navigation ----------------------------------------->

<!-- START Page Alerts -------------------------------------------->
<?php include __DIR__ . '/pageAlerts.php'; ?>
