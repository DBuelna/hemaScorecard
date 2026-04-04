<!-- START Lower Navigation --------------------------------------->
<?php
if (
	($_SESSION['eventID'] != null && $_SESSION['tournamentID'] != null) &&
	(!isset($hideEventNav) || ALLOW['SOFTWARE_ADMIN'] == true)
) {

	/* This is the lower navigational bar
	 * It will not show up if there is no event or tournament selected,
	 * or if the pagerequests it to be hidden. It will always be shown
	 * if logged in as a super admin.
	 * The items that appear in the navigation bar change depending on
	 * the type of tournament which is active. */
	$navBarString = '';

	if ($_SESSION['tournamentID'] != null){
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
		$navBarString .= "<li><a href='participantsComponents.php'>Tournament Components</a></li>
							<li><a href='poolStandings.php'>Standings</a></li>";
	}

	// Tournament has pools
	if(isPools($_SESSION['tournamentID'])){
		$navBarString .= "<li><a href='poolRosters.php'>Pool Rosters</a></li>
							<li><a href='poolMatches.php'>Pool Matches</a></li>
							<li><a href='poolStandings.php'>Pool Standings</a></li>";
	} elseif ($_SESSION['formatID'] == FORMAT_MATCH
				&& ALLOW['EVENT_MANAGEMENT'] == true){

		$navBarString .= "<li><a href='poolRosters.php'>Create Pools</a></li>";
	}

	// Tournament has brackets
	if(isBrackets($_SESSION['tournamentID'])){
		$navBarString .= "<li><a href='finalsBracket.php'>Finals Bracket</a></li>";
	} elseif ($_SESSION['formatID'] == FORMAT_MATCH
				&& ALLOW['EVENT_MANAGEMENT'] == true){

		$navBarString .= "<li><a href='finalsBracket.php'>Create Bracket</a></li>";
	}

	// Tournament has rounds
	if ($_SESSION['formatID'] == FORMAT_SOLO) {
		$navBarString .= "<li><a href='roundRosters.php'>Round Rosters</a></li>
							<li><a href='roundMatches.php'>Round Scores</a></li>
							<li><a href='roundStandings.php'>Round Standings</a></li>";
	}

	// Tournament has cutting quallification
	if (isCuttingQual($_SESSION['tournamentID'])) {
		$navBarString .= "<li><a href='cutQualsTournament.php'>Cutting Qualification</a></li>";
	}

	if (isset($navBarString)) { ?>
		<ul class='menu align-left tourney-menu-large show-for-medium'>
			<?= $navBarString ?>
		</ul>
		<ul class='dropdown menu tourney-menu-mobile
			show-for-small-only align-center' data-dropdown-menu>
			<li>
				<a href='#'>Browse Tournament</a>
				<ul class='menu'>
					<?= $navBarString ?>
				</ul>
			</li>
		</ul>
	<?php }
}
?>
<!-- END Lower Navigation ----------------------------------------->
