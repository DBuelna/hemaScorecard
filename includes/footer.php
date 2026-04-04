<?php
/*******************************************************************************
	Footer

	Page footer and javascript declarations

*******************************************************************************/

	displayPageAlerts();

?>

	</div id='a'><!-- End Page Wrapper -->

<!-- Footer content -->
	<?php if(isset($hideFooter) == false): ?>

	<?=displaySponsors()?>

	<style>
		.site-footer {
			border-top: 1px solid black;
			margin-top: 20px;
			padding: 18px 0 8px;
		}

		.site-footer__inner {
			display: flex;
			flex-wrap: wrap;
			gap: 16px 18px;
			align-items: center;
			justify-content: center;
		}

		.site-footer__group {
			display: flex;
			flex: 1 1 260px;
			gap: 14px;
			align-items: center;
			justify-content: center;
			min-width: 0;
		}

		.site-footer__text {
			line-height: 1.45;
		}

		.site-footer__logo {
			max-width: 84px;
			height: auto;
			display: block;
		}

		.site-footer a {
			overflow-wrap: anywhere;
		}

		@media (max-width: 640px) {
			.site-footer {
				padding-top: 16px;
			}

			.site-footer__inner {
				flex-direction: column;
				align-items: stretch;
				gap: 18px;
			}

			.site-footer__group {
				justify-content: center;
				flex-basis: auto;
			}
		}
	</style>

	<div class='site-footer'>
		<div class='site-footer__inner'>
			<div class='site-footer__group'>
				<div class='site-footer__text'>
					<a href='index.php'>HEMA Scorecard</a><br>
					Developed by Sean Franklin<br>
					A <a href='http://www.swordstem.com/'>SwordSTEM</a> project<br>
					<a href='http://www.seanfranklin.ca/talenttree' class='easter-egg'>you found me</a>
				</div>
				<a href='http://www.swordstem.com/'>
					<img class='site-footer__logo' src='includes/images/SwordSTEM_logo.png' alt='SwordSTEM logo'>
				</a>
			</div>

			<div class='site-footer__group'>
				<div class='site-footer__text'>
					Supported by the<br>
					<a href='https://www.hemaalliance.com/'>HEMA Alliance</a>
				</div>
				<a href='https://www.hemaalliance.com/'>
					<img class='site-footer__logo' src='includes/images/hemaa_logo_s.png' alt='HEMA Alliance logo'>
				</a>
			</div>
		</div>
	</div>
	<?php endif ?>

<!-- Start Scripts -->
	<script src="includes/foundation/js/vendor/jquery.js"></script>
	<script src="includes/foundation/js/vendor/what-input.js"></script>
	<script src="includes/foundation/js/vendor/foundation.js"></script>
	<script src="includes/foundation/js/app.js<?=$vJ?>"></script>

	<script src="https://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>


	<script type='text/javascript' src='includes/scripts/general_scripts.js<?=$vJ?>'></script>
	<script type='text/javascript' src='includes/scripts/delete_checking_scripts.js<?=$vJ?>'></script>


	<?php
		if(isset($jsIncludes)){
			foreach((array)$jsIncludes as $includePath){
				echo "<script type='text/javascript' src='includes/scripts/{$includePath}{$vJ}'></script>";
			}
		}
	?>


	<?php if(isset($createSortableDataTable)): ?>

		<script src='https://code.jquery.com/jquery-3.3.1.js'></script>
		<script src='https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js'></script>
		<script src='https://cdn.datatables.net/1.10.19/js/dataTables.foundation.min.js'></script>

		<script>
		<?php foreach($createSortableDataTable as $table):
			$tableName = $table[0];
			$tableSize = $table[1];
			$tableSortCol = @(int)$table[2];
			$tableSortOrder = @$table[3];
			if($tableSortOrder == 'asc' || $tableSortOrder == 'desc'){
				$order = "order: [[{$tableSortCol}, '{$tableSortOrder}']],";
			} else {
				$order = "";
			}


			?>

			$(document).ready(function() {
				$('#<?=$tableName?>').DataTable({
					"pageLength": <?=$tableSize?>,
					stateSave: true,
					<?=$order?>
				});
			} );

		<?php endforeach ?>

		google.charts.load('current', {'packages':['corechart']});

		</script>

	<?php endif ?>

<!-- End Scripts -->


	<script>
		if ( $( "#termsOfUseModal" ).length ) {
			$(document).ready(function(){$('#termsOfUseModal').foundation('open');});
		}
	</script>

</body>


</html>

<?php

// FUNCTIONS ///////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

/******************************************************************************/

function displaySponsors(){

	$eventSponsors = getEventSponsors($_SESSION['eventID']);
	$imageSize = 2; // 200 / 100% = 2 per percent

	if(sizeof($eventSponsors) == 0){
		return;
	}

	$pageName = basename($_SERVER['PHP_SELF']);
	if(ALLOW['EVENT_SCOREKEEP'] == true && ($pageName == 'scoreMatch.php' || $pageName == 'scorePiece.php' )){
		$hideForSmall = 'hide-for-small-only';
	} else {
		$hideForSmall = '';
	}


?>
	<div class="large-12 cell align-top <?=$hideForSmall?>" style='border-top: 1px solid black; margin-top: 20px;'>

		<div class='grid-x grid-margin-x align-center align-top' id='sponsor-large'>


				<?php foreach($eventSponsors as $sponsor): ?>
					<div class='shrink cell'>
					<img class='align-self-top' src="<?=$sponsor['imagePathAndFile']?>" style='display: inline-block'
						width="<?=($sponsor['eventSponsorPercent'] * $imageSize)?>" title="<?=$sponsor['sponsorName']?>">
					</div>
				<?php endforeach ?>

		</div>
	</div>

<?
}

/******************************************************************************/

// END OF DOCUMENT /////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

?>
