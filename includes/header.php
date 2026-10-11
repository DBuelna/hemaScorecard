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
    <!-- Output base URL of site for Javascript use-->
    <script>
        <?php echo "var BASE_URL = '".BASE_URL."';";?>
    </script>

    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="
        HEMA Scorecard is a free online software application for running
        Historical European Martial Arts tournaments and making the information
        easily accessible.
    ">
    <meta name="keywords" content="HEMA, Tournament, Historical European Martial Arts, Martial Arts, Sword">
    <title>HEMA Scorecard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.3/css/foundation.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.foundation.min.css">

    <link href="https://fonts.googleapis.com/css?family=Chivo:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="includes/foundation/css/app.css">
    <link rel="stylesheet" href="includes/foundation/css/custom.css<?=$vC?>">

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>google.charts.load('current', {'packages':['corechart']});</script>
    <script src="https://cdn.tiny.cloud/1/ctrvec03t4hztqmygiaf7d6mtiod1qat9px92nlsxdq2mat3/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

    <link rel='icon' href='includes\images\favicon.png'>

    <!-- Jumps to section on page if $_SESSION['jumpTo'] is set -->
    <?php 
    if (isset($_SESSION['jumpTo'])): ?>
        <script>window.onload = window.location.hash='<?=$_SESSION['jumpTo']?>';</script><?php 

        unset($_SESSION['jumpTo']);
    endif;

    if (isset($refreshPageTimer) == true && (int)$refreshPageTimer != 0):?>
        <meta http-equiv="refresh" content="<?=(int)$refreshPageTimer?>"><?php 
    endif;
    ?>
    <style>
        li.fighter_1_color {
            border-bottom-color: <?= COLOR_CODE_1 ?>;
        }
        li.fighter_2_color {
            border-bottom-color: <?= COLOR_CODE_2 ?>;
        }
        .f1-BG {
            background-color: <?= COLOR_CODE_1 ?>;
        }
        .f2-BG {
            background-color: <?= COLOR_CODE_2 ?>;
        }
    </style>
</head>

<body>

<!-- START Upper Navigation ------------------------------------------>
<?php debugging(); ?>

<!-- Mobile Navigation -->
<div class="title-bar" data-responsive-toggle="tourney-animated-menu" data-hide-for="large" style='display:none'>
    <form method='POST' name='logOutForm1'>
    <button class="menu-icon" type="button" data-toggle></button>
    <div class="title-bar-title">Menu</div>

    <?php if($_SESSION['userName'] == null): ?>
        <a href='adminLogIn.php' class='login-link'>Login</a>
    <?php else: ?>
        <input type='hidden' name='formName' value='logUserOut'>
        <a href='javascript:document.logOutForm1.submit();' class='login-link'>Log Out</a>
    <?php endif ?>
    </form>
</div>

<!-- Full Navigation -->
<div class="top-bar" id="tourney-animated-menu" data-animate="hinge-in-from-top hinge-out-from-top" style='display:none'>
    <div class="top-bar-left">
        <ul class="dropdown menu vertical medium-horizontal" data-dropdown-menu style='z-index: 4;'>
            <li><div class="drop-down-separator show-for-small-only">Tournament</div></li>
            <?php tournamentListForHeader(); ?>
            <?=menuEvent()?>
            <?=menuTournament()?>
            <?=menuEventOrg()?>
            <?=activeLivestream()?>
            <li><div class="drop-down-separator show-for-small-only">Software</div></li>
            <li style='border-right: 1px solid white' class='hide-for-small-only'>&nbsp;</li>
            <?=menuAnalytics()?>
            <!--<li><a style='margin-left:5px;' class='button warning hollow no-bottom' href='statsYear.php'>2024 in Review</a></li>-->
            <li class="white-text"><a href='infoSelect.php'>Change Event</a></li>
            <li class="white-text"><a href='adminHelp.php'>Help/About</a></li>
            <?=menuAdmin()?>
        </ul>
    </div>

    <div class="top-bar-right show-for-large">
        <?php if($_SESSION['userName'] == null): ?>
            <a href='adminLogIn.php' style='color:white'>Login</a>
        <?php else: ?>
            <form method='POST' name='logOutForm2'>
                <input type='hidden' name='formName' value='logUserOut'>
                <a href='javascript:document.logOutForm2.submit();' style='color:white'>Log Out</a>
            </form>
        <?php endif ?>
    </div>
</div>

<?=DisplayServerVersion()?>
<!-- END Upper Navigation ----------------------------------------->

<!-- START Page Title --------------------------------------------->
<?php 
if (($_SESSION['eventID'] != null && !isset($hidePageTitle)) || (isset($forcePageTitle)) ): ?>
    <div class='hero-title'>
        <!-- Event Name -->
        <h1><?php eventNameForHeader(); ?></h1>
        <!-- Tournament Name -->
        <?php  
        if (isset($includeTournamentName) && $_SESSION['tournamentID'] != null):
            $tName = getTournamentName(); ?>
            <div class='hide-for-large'>
                <i><?= $tName ?></i>
            </div><?php 
        endif 
        ?>

        <!-- Page Name -->
        <h2><?= $pageName ?></h2>
    </div><?php 
else: ?>
    <BR><?php 
endif 
?>
<!-- END Page Title ----------------------------------------------->

<!-- START Lower Navigation --------------------------------------->
<div id='page-wrapper' class='grid-container'>
<?php
if (($_SESSION['eventID'] != null && $_SESSION['tournamentID'] != null)
    && (!isset($hideEventNav) || ALLOW['SOFTWARE_ADMIN'] == true)):
    /* This is the lower navigational bar
     * It will not show up if there is no event or tournament selected,
     * or if the pagerequests it to be hidden. It will always be shown
     * if logged in as a super admin.
     * The items that appear in the navigation bar change depending on
     * the type of tournament which is active. */

    $navBarString = '';

    if($_SESSION['tournamentID'] != null){
        if(!isTeams($_SESSION['tournamentID'])){
            $navBarString .= "<li><a href='participantsTournament.php'>Tournament Roster</a></li>";
        } elseif(ALLOW['EVENT_SCOREKEEP'] == true || ALLOW['VIEW_SETTINGS'] == true) {
            $navBarString .= "<li><a href='participantsTournament.php'>Tournament Roster</a></li>";
            $navBarString .= "<li><a href='participantsTeams.php'>Team Rosters</a></li>";
        } else {
            $navBarString .= "<li><a href='participantsTeams.php'>Team Rosters</a></li>";
        }
    }

    // Tournament is a meta-tournament
    if($_SESSION['formatID'] == FORMAT_META){
        $navBarString .= "
            <li>
                <a href='participantsComponents.php'>Tournament Components</a>
            </li>
            <li><a href='poolStandings.php'>Standings</a></li>";
    }

    // Tournament has pools
    if (isPools($_SESSION['tournamentID']))
    {
        $navBarString .= "
            <li>
                <a href='poolRosters.php'>Pool Rosters</a></li>
            <li>
                <a href='poolMatches.php'>Pool Matches</a></li>
            <li>
                <a href='poolStandings.php'>Pool Standings</a></li>";
    } elseif ($_SESSION['formatID'] == FORMAT_MATCH && ALLOW['EVENT_MANAGEMENT'] == true) {
        $navBarString .= "<li><a href='poolRosters.php'>Create Pools</a></li>";
    }

    // Tournament has brackets
    if (isBrackets($_SESSION['tournamentID'])) {
        $navBarString .= "<li><a href='finalsBracket.php'>Finals Bracket</a></li>";
    } elseif ($_SESSION['formatID'] == FORMAT_MATCH && ALLOW['EVENT_MANAGEMENT'] == true){
        $navBarString .= "<li><a href='finalsBracket.php'>Create Bracket</a></li>";
    }

    // Tournament has rounds
    if ($_SESSION['formatID'] == FORMAT_SOLO) {
        $navBarString .= "
            <li><a href='roundRosters.php'>Round Rosters</a></li>
            <li><a href='roundMatches.php'>Round Scores</a></li>
            <li><a href='roundStandings.php'>Round Standings</a></li>";
    }

    // Tournament has cutting quallification
    if (isCuttingQual($_SESSION['tournamentID'])){
        $navBarString .= "<li><a href='cutQualsTournament.php'>Cutting Qualification</a></li>";
    }

    if  (isset($navBarString)): ?>
        <ul class='menu align-left tourney-menu-large show-for-medium'>
            <?= $navBarString ?>
        </ul>
        <ul class='dropdown menu tourney-menu-mobile show-for-small-only align-center' data-dropdown-menu>
            <li>
                <a href='#'>Browse Tournament</a>
                <ul class='menu'>
                    <?= $navBarString ?>
                </ul>
            </li>
        </ul><?php 
    endif;
endif ;
?>
<!-- END Lower Navigation ----------------------------------------->

<!-- START Page Alerts -------------------------------------------->
<?php include __DIR__ . '/pageAlerts.php'; ?>
