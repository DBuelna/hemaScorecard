<!-- START Upper Navigation ------------------------------------------>

<?php debugging(); ?>

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
