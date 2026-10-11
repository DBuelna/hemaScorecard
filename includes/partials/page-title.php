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
