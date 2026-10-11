<?php

if (isset($lockedTournamentWarning)) {
    tournamentLockedAlert($lockedTournamentWarning);
}

displayPageAlerts();
displayEventAnnouncements();
