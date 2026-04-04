<!-- Output base URL of site for Javascript use-->
<script><?="var BASE_URL = '".BASE_URL."';";?></script>
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
<?php if(isset($_SESSION['jumpTo'])): ?>
	<script>window.onload = window.location.hash='<?=$_SESSION['jumpTo']?>';</script>
	<?php unset($_SESSION['jumpTo']); ?>
<?php endif ?>

<?php if(isset($refreshPageTimer) == true && (int)$refreshPageTimer != 0):?>
	<meta http-equiv="refresh" content="<?=(int)$refreshPageTimer?>">
<?php endif ?>

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
