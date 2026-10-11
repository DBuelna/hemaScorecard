<?php if ($_SESSION['eventID'] == null):
    pageError('event');
else: ?>

	<?php if($anyFinalized == false){
		showEventDescription();
	}?>

	<div class='grid-x grid-margin-x'>

		<?=manualTournamentPlacing((int)@$_SESSION['manualPlacing']['tournamentID'])?>

		<?=showEventBurgees()?>

		<?=showTournamentPlacings($tournamentsToDisplay,  $tournamentList)?>

	</div>

	<?php if($anyFinalized == true){
		showEventDescription();
	}?>

<?php endif;

unset($_SESSION['manualPlacingMessage']);

