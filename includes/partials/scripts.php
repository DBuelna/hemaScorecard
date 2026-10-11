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

